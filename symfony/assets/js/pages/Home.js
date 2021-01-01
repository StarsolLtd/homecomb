import React from 'react';
import $ from 'jquery';
import 'jquery-ui-bundle';
import PropertyAutocomplete from "../components/PropertyAutocomplete";
import TextLogo from "../components/TextLogo";
import {Col, Container, Form, FormGroup, Label, Row} from "reactstrap";
import '../../styles/home.scss';
import Constants from "../Constants";
import Header from "../layout/Header";

class Home extends React.Component {
    constructor() {
        super();
        this.state = {};
    }

    componentDidMount() {
        document.title = Constants.SITE_NAME + ' | Tenant Reviews of Lettings Agents and Properties';

        const getScrollPosition = (el = window) => ({
            x: el.pageXOffset !== undefined ? el.pageXOffset : el.scrollLeft,
            y: el.pageYOffset !== undefined ? el.pageYOffset : el.scrollTop
        });

        const classScrolledDown = 'bg-primary';

        window.onscroll = function () {
            let pos = getScrollPosition(window);
            let headerNavbar = $('#header-navbar');
            if (pos.y >= 50) {
                if (!headerNavbar.hasClass(classScrolledDown)) {
                    headerNavbar.addClass(classScrolledDown, 500);
                }
            } else if (headerNavbar.hasClass(classScrolledDown)) {
                headerNavbar.removeClass(classScrolledDown, 500);
            }
        };
    }

    render() {
        return (
            <Row id="home-background" className="no-gutters w-100">
                <Header className="fixed-top" />
                <Col id="home" className="align-self-center text-center mt-7 mb-5">
                    <Container className="rounded-lg bg-light-translucent-90 p-5 mt-5 mb-5">
                        <h1 className="logo-large"><TextLogo /></h1>
                        <Form>
                            <FormGroup>
                                <Label for="propertySearch">Find tenant reviews for properties and lettings agents</Label>
                                <PropertyAutocomplete />
                            </FormGroup>
                            <p>
                                After you've entered a few characters, you will see suggested results
                            </p>
                        </Form>
                    </Container>
                </Col>
            </Row>
        );
    }
}

export default Home;
