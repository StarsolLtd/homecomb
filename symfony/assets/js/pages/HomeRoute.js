import React, {Fragment} from 'react';
import {Redirect} from 'react-router-dom';
import Header from "../layout/Header";
import {Col, Container, Row} from "reactstrap";
import HowItWorks from "../content/HowItWorks";

class HomeRoute extends React.Component {
    constructor(props) {
        super(props);
    }

    render() {
        const Component = this.props.render;
        return (
            <Fragment>
                <div id="home-background" className="w-100 d-flex clearfix">
                    {/*<Header className="w-100 bg-light-translucent-0 mb-lg-5 navbar-fixed-top" textLogoClassName="logo-white"/>*/}
                    <Component {...this.props}/>
                </div>

            </Fragment>
        )
    }

}


export default HomeRoute;