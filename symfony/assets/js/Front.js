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
import Survey from "./pages/Survey";
import TenancyReview from "./pages/TenancyReview";
import HowItWorks from "./content/HowItWorks";
import FooterLarge from "./layout/FooterLarge";
import View from "./pages/View";
import LatestReviews from "./content/LatestReviews";
import FindByPostcode from "./pages/FindByPostcode";

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
                            : <Header className="bg-gradient-primary include-search"/>
                        }
                      />
                    <Route path="/" exact component={Header}/>
                </Switch>
                <div className="wrapper flex-grow-1 d-flex">
                    <Switch>
                        <Route path="/" exact component={Home}/>
                        <Route path="/about" render={(props) => <View content={About} {...props} />}/>
                        <Route path="/contact" render={(props) => <View content={Contact} {...props} />}/>
                        <Route path="/find-by-postcode" render={(props) => <View content={FindByPostcode} {...props} />}/>
                        <Route path="/privacy-policy" render={(props) => <View content={PrivacyPolicy} {...props} />}/>
                        <Route path="/register" render={(props) => <View content={Register} {...props} />}/>
                        <Route path="/review" exact render={(props) => <View content={TenancyReview} {...props} />}/>
                        <Route path="/agency/:slug" render={(props) => <View content={AgencyView} {...props} />}/>
                        <Route path="/branch/:slug" render={(props) => <View content={BranchView} {...props} />}/>
                        <Route path="/l/:slug" render={(props) => <View content={LocaleView} {...props} />}/>
                        <Route path="/property/:slug" render={(props) => <View content={PropertyView} {...props} />}/>
                        <Route path="/rs/:code" render={(props) => <View content={CreateReview} {...props} />}/>
                        <Route path="/review-your-tenancy/:code" render={(props) => <View content={CreateReview} {...props} />}/>
                        <Route path="/s/:slug" render={(props) => <View content={Survey} {...props} />}/>

                    </Switch>
                </div>

                <Switch>
                    <Route
                        render={({ location }) => this.showHowItWorks(location.pathname)
                            ? <HowItWorks />
                            : null
                        }
                    />
                </Switch>

                <Switch>
                    <Route
                        render={({ location }) => this.showLatestReviews(location.pathname)
                            ? <LatestReviews />
                            : null
                        }
                    />
                </Switch>

                <FooterLarge user={this.state.user}/>
            </Fragment>
        )
    }

    showHowItWorks(pathname) {
        if (['/', '/about', '/contact', '/find-by-postcode'].includes(pathname)) {
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

    showLatestReviews(pathname) {
        if (['/', '/about'].includes(pathname)) {
            return true;
        }

        return false;
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