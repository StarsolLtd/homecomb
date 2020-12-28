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

function Front() {
    return (
        <Fragment>
            <Header />
            <div className="wrapper flex-grow-1">
                <Switch>
                    <Route path="/about" component={About} />
                    <Route path="/contact" component={Contact} />
                    <Route path="/agency/:slug" component={AgencyView} />
                </Switch>
            </div>
            <Footer />
        </Fragment>
    )
}

export default Front;