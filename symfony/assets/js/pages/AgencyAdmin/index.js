import React from "react"
import ReactDOM from 'react-dom'
import {Switch, Route, BrowserRouter} from 'react-router-dom';

import $ from 'jquery';
import 'jquery-ui-bundle';
import CreateAgency from "./CreateAgency";
import CreateBranch from "./CreateBranch";
import UpdateAgency from "./UpdateAgency";
import UpdateBranch from "./UpdateBranch";
import CreateReviewSolicitation from "./CreateReviewSolicitation";

import Dashboard from "./Dashboard";
import LayoutFooter from "./LayoutFooter";
import LayoutHeader from "./LayoutHeader";
import {Col, Row} from "reactstrap";

import '../../../styles/app.scss';
import '../../../styles/AgencyAdmin/style.scss';
import View from "./View";
import AgentRoute from "./AgentRoute";
import NonAgentRoute from "./NonAgentRoute";

class Index extends React.Component {

    constructor() {
        super();
        this.state = {
            user: {
                agencyAdmin: false
            },
            userDataFetched: false
        };
    }

    componentDidMount() {
        this.fetchUserData();
    }

    render() {
        return (
            <BrowserRouter>
                <LayoutHeader user={this.state.user}/>
                <Row className="flex-grow-1 d-flex">
                    <Col md={12} className="p-4">
                        {this.state.userDataFetched &&
                        <Switch>
                            <NonAgentRoute isAgencyAdmin={this.state.user.agencyAdmin} path="/verified/agency/create" render={
                                (props) => <View content={CreateAgency} />
                            }/>
                            <AgentRoute isAgencyAdmin={this.state.user.agencyAdmin} path="/verified/agency" render={
                                (props) => <View content={UpdateAgency} />
                            }/>
                            <AgentRoute isAgencyAdmin={this.state.user.agencyAdmin} path="/verified/dashboard" render={
                                (props) => <View content={Dashboard} />
                            }/>
                            <AgentRoute isAgencyAdmin={this.state.user.agencyAdmin} path="/verified/branch" exact render={
                                (props) => <View content={CreateBranch} />
                            }/>
                            <AgentRoute isAgencyAdmin={this.state.user.agencyAdmin} path="/verified/branch/:slug" render={
                                (props) => <View content={UpdateBranch} {...props} />
                            }/>
                            <AgentRoute isAgencyAdmin={this.state.user.agencyAdmin} path="/verified/request-review" render={
                                (props) => <View content={CreateReviewSolicitation} />
                            }/>
                        </Switch>
                        }
                    </Col>
                </Row>
                <LayoutFooter user={this.state.user}/>
            </BrowserRouter>
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
                    user: data,
                    userDataFetched: true,
                });
            })
            .catch(err => console.error("Error:", err));
    }
}

ReactDOM.render(<Index />, document.getElementById('root'));