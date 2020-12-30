import React from "react"
import ReactDOM from 'react-dom'
import {Switch, Route, BrowserRouter} from 'react-router-dom';

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

import '../../../styles/app.scss';
import '../../../styles/AgencyAdmin/style.scss';

class Index extends React.Component {

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
            <BrowserRouter>
                <LayoutHeader/>
                <Row className="flex-grow-1 d-flex">
                    <Col md={12} className="p-4">
                        <Switch>
                            <Route path="/verified/agency/create" component={CreateAgency}/>
                            <Route path="/verified/agency" component={UpdateAgency}/>
                            <Route path="/verified/agency-admin" component={AgencyAdminHome}/>
                            <Route path="/verified/request-review" component={CreateReviewSolicitation}/>
                        </Switch>
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
                    user: data
                });
            })
            .catch(err => console.error("Error:", err));
    }
}

ReactDOM.render(<Index />, document.getElementById('root'));