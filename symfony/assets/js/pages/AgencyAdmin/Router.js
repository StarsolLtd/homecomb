import React, {Fragment} from "react"
import {Switch, Route} from 'react-router-dom';

import Header from "../../layout/Header";

import $ from 'jquery';
import 'jquery-ui-bundle';
import CreateAgency from "./CreateAgency";
import UpdateAgency from "./UpdateAgency";
import CreateReviewSolicitation from "../CreateReviewSolicitation";

import AgencyAdminHome from "./Home";
import Footer from "./Footer";


class Router extends React.Component {

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
                    <Header/>
                </Switch>
                <div className="wrapper flex-grow-1 d-flex">
                    <Switch>
                        <Route path="/verified/agency/create" component={CreateAgency}/>
                        <Route path="/verified/agency" component={UpdateAgency}/>
                        <Route path="/verified/agency-admin" component={AgencyAdminHome}/>
                        <Route path="/verified/request-review" component={CreateReviewSolicitation}/>
                    </Switch>
                </div>
                <Footer user={this.state.user}/>
            </Fragment>
        )
    }

    fetchUserData() {
        fetch(
            '/api/user',
            {
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                }
            }
        )
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

export default Router;