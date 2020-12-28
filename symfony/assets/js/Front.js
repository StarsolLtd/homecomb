import React, {Fragment} from "react"
import {Switch, Route} from 'react-router-dom';

import About from "./pages/About"
import Contact from "./pages/Contact"
import Footer from "./layout/Footer";
import Header from "./layout/Header";

import $ from 'jquery';
import 'jquery-ui-bundle';
import AgencyView from "./pages/AgencyView";
import BranchView from "./pages/BranchView";
import CreateAgency from "./pages/CreateAgency";
import CreateReviewSolicitation from "./pages/CreateReviewSolicitation";
import Home from "./pages/Home";
import LocaleView from "./pages/LocaleView";
import PropertyView from "./pages/PropertyView";
import PrivacyPolicy from "./pages/PrivacyPolicy";

class Front extends React.Component {

    constructor(props) {
        super(props);

        console.log(window.location.pathname);

        this.state = {};
    }

    render() {
        return (
            <Fragment>
                <Switch>
                    <Route
                        render={({ location }) => ['/'].includes(location.pathname)
                            ? null
                            : <Header/>
                        }
                      />
                    <Route path="/" exact component={Header}/>
                </Switch>
                <div className="wrapper flex-grow-1 d-flex">
                    <Switch>
                        <Route path="/" exact component={Home}/>
                        <Route path="/about" component={About}/>
                        <Route path="/contact" component={Contact}/>
                        <Route path="/privacy-policy" component={PrivacyPolicy}/>
                        <Route path="/agency/:slug" component={AgencyView}/>
                        <Route path="/branch/:slug" component={BranchView}/>
                        <Route path="/l/:slug" component={LocaleView}/>
                        <Route path="/property/:slug" component={PropertyView}/>

                        <Route path="/verified/agency" component={CreateAgency}/>
                        <Route path="/verified/request-review" component={CreateReviewSolicitation}/>
                    </Switch>
                </div>
                <Footer/>
            </Fragment>
        )
    }
}

export default Front;