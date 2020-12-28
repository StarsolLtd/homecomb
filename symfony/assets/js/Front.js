import React, {Fragment} from "react"

import About from "./pages/About"
import Contact from "./pages/Contact"
import {
    Switch,
    Route,
} from "react-router-dom";
import Footer from "./layout/Footer";
import Header from "./layout/Header";

import $ from 'jquery';
import 'jquery-ui-bundle';
import AgencyView from "./pages/AgencyView";
import BranchView from "./pages/BranchView";
import CreateAgency from "./pages/CreateAgency";
import CreateReviewSolicitation from "./pages/CreateReviewSolicitation";
import LocaleView from "./pages/LocaleView";
import PropertyView from "./pages/PropertyView";

function Front() {
    return (
        <Fragment>
            <Header />
            <div className="wrapper flex-grow-1">
                <Switch>
                    <Route path="/about" component={About} />
                    <Route path="/contact" component={Contact} />
                    <Route path="/agency/:slug" component={AgencyView} />
                    <Route path="/branch/:slug" component={BranchView} />
                    <Route path="/l/:slug" component={LocaleView} />
                    <Route path="/property/:slug" component={PropertyView} />

                    <Route path="/verified/agency" component={CreateAgency} />
                    <Route path="/verified/request-review" component={CreateReviewSolicitation} />
                </Switch>
            </div>
            <Footer />
        </Fragment>
    )
}

export default Front;