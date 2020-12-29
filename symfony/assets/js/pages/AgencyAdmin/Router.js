import React, {Fragment} from "react"
import {Switch, Route} from 'react-router-dom';

import $ from 'jquery';
import 'jquery-ui-bundle';
import CreateAgency from "./CreateAgency";
import UpdateAgency from "./UpdateAgency";
import CreateReviewSolicitation from "../CreateReviewSolicitation";

import AgencyAdminHome from "./Home";
import LayoutFooter from "./LayoutFooter";
import LayoutHeader from "./LayoutHeader";
import LayoutSidebar from "./LayoutSidebar";
import {Col, Row} from "reactstrap";


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
                <LayoutHeader/>
                <Row className="flex-grow-1 d-flex">
                    <LayoutSidebar />
                    <Col md={10} className="p-4">
                        <Switch>
                            <Route path="/verified/agency/create" component={CreateAgency}/>
                            <Route path="/verified/agency" component={UpdateAgency}/>
                            <Route path="/verified/agency-admin" component={AgencyAdminHome}/>
                            <Route path="/verified/request-review" component={CreateReviewSolicitation}/>
                        </Switch>
                    </Col>
                </Row>
                <LayoutFooter user={this.state.user}/>
            </Fragment>
        )
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

export default Router;