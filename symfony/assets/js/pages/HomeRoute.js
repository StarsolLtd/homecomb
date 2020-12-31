import React, {Fragment} from 'react';
import {Redirect} from 'react-router-dom';
import Header from "../layout/Header";
import {Col, Row} from "reactstrap";

class HomeRoute extends React.Component {
    constructor(props) {
        super(props);
    }

    render() {
        const Component = this.props.render;
        return (
            <div id="home-background" className="w-100 d-flex">
                {/*<Header className="w-100 bg-light-translucent-0 mb-lg-5 navbar-fixed-top" textLogoClassName="logo-white"/>*/}
                <Component {...this.props}/>
            </div>
        )
    }

}


export default HomeRoute;