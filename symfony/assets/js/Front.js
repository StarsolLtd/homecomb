import React, {Fragment} from "react"
import {Switch, Route} from 'react-router-dom';

import About from "./pages/About"
import Contact from "./pages/Contact"
import Header from "./layout/Header";

import 'jquery-ui-bundle';
import AgencyView from "./pages/AgencyView";
import BranchView from "./pages/BranchView";
import CreateReview from "./pages/CreateReview";
import Home from "./pages/Home";
import LocaleView from "./pages/LocaleView";
import PropertyView from "./pages/PropertyView";
import PrivacyPolicy from "./pages/PrivacyPolicy";
import Register from "./pages/Register";
import TenancyReview from "./pages/TenancyReview";
import HowItWorks from "./content/HowItWorks";
import FooterLarge from "./layout/FooterLarge";
import View from "./pages/View";

class Front extends React.Component {

    constructor() {
        super();
        this.state = {
            user: null
        };
    }

    componentDidMount() {
        this.fetchUserData();
    }

    render() {
        return (
            <Fragment>
                <Switch>
                    <Route
                        render={({ location }) => ['/'].includes(location.pathname)
                            ? null
                            : <Header className="bg-gradient-primary"/>
                        }
                      />
                    <Route path="/" exact component={Header}/>
                </Switch>
                <div className="wrapper flex-grow-1 d-flex">
                    <Switch>
                        <Route path="/" exact component={Home}/>
                        <Route path="/about" render={(props) => <View content={About} {...props} />}/>
                        <Route path="/contact" render={(props) => <View content={Contact} {...props} />}/>
                        <Route path="/privacy-policy" render={(props) => <View content={PrivacyPolicy} {...props} />}/>
                        <Route path="/register" render={(props) => <View content={Register} {...props} />}/>
                        <Route path="/review" exact render={(props) => <View content={TenancyReview} {...props} />}/>
                        <Route path="/agency/:slug" render={(props) => <View content={AgencyView} {...props} />}/>
                        <Route path="/branch/:slug" render={(props) => <View content={BranchView} {...props} />}/>
                        <Route path="/l/:slug" render={(props) => <View content={LocaleView} {...props} />}/>
                        <Route path="/property/:slug" render={(props) => <View content={PropertyView} {...props} />}/>
                        <Route path="/rs/:code" render={(props) => <View content={CreateReview} {...props} />}/>
                        <Route path="/review-your-tenancy/:code" render={(props) => <View content={CreateReview} {...props} />}/>
                    </Switch>
                </div>

                <Switch>
                    <Route
                        render={({ location }) => this.showHowItWorks(location.pathname)
                            ? <HowItWorks />
                            : null
                        }
                    />
                    <Route path="/" exact component={Header}/>
                </Switch>

                <FooterLarge user={this.state.user}/>
            </Fragment>
        )
    }

    showHowItWorks(pathname) {
        if (['/', '/about', '/contact'].includes(pathname)) {
            return true;
        }

        let matched = false;
        ['/agency/', '/branch/', '/property/', '/l/', '/rs/'].forEach(function(item){
            if (pathname.startsWith(item)) {
                matched = true;
            }
        })

        return matched;
    }

    fetchUserData() {
        fetch('/api/user')
            .then((response) => {
                if (!response.ok) throw new Error(response.status);
                else return response.json();
            })
            .then(data => {
                this.setState({
                    user: data
                });
            })
            .catch(err => console.error("Error:", err));
    }
}

export default Front;