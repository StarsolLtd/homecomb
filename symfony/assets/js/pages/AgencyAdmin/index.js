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

import AgencyAdminHome from "./Home";
import LayoutFooter from "./LayoutFooter";
import LayoutHeader from "./LayoutHeader";
import {Col, Row} from "reactstrap";

import '../../../styles/app.scss';
import '../../../styles/AgencyAdmin/style.scss';
import View from "./View";
import AgencyAdminPrivateRoute from "../../components/AgencyAdminPrivateRoute";

class Index extends React.Component {

    constructor() {
        super();
        this.state = {
            user: null,
            userDataFetched: false
        };
    }

    componentDidMount() {
        this.fetchUserData();
    }

    render() {
        return (
            <BrowserRouter>
                <LayoutHeader/>
                <Row className="flex-grow-1 d-flex">
                    <Col md={12} className="p-4">
                        {this.state.userDataFetched &&
                        <Switch>
                            <Route path="/verified/agency/create" render={
                                (props) => <View content={CreateAgency} />
                            }/>
                            <AgencyAdminPrivateRoute authed={this.state.user.agencyAdmin} path="/verified/agency" render={
                                (props) => <View content={UpdateAgency} />
                            }/>
                            <AgencyAdminPrivateRoute authed={this.state.user.agencyAdmin} path="/verified/agency-admin" render={
                                (props) => <View content={AgencyAdminHome} />
                            }/>
                            <AgencyAdminPrivateRoute authed={this.state.user.agencyAdmin} path="/verified/branch" exact render={
                                (props) => <View content={CreateBranch} />
                            }/>
                            <AgencyAdminPrivateRoute authed={this.state.user.agencyAdmin} path="/verified/branch/:slug" render={
                                (props) => <View content={UpdateBranch} {...props} />
                            }/>
                            <AgencyAdminPrivateRoute authed={this.state.user.agencyAdmin} path="/verified/request-review" render={
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