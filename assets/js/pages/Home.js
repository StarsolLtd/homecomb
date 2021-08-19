import React, {Fragment} from 'react';
import PropertyAutocomplete from "../components/PropertyAutocomplete";
import TextLogo from "../components/TextLogo";
import {Col, Container, Form, FormGroup, Label, Row} from "reactstrap";
import '../../styles/home.scss';
import Constants from "../Constants";
import Header from "../layout/Header";
import FlashMessages from "../layout/FlashMessages";
import {addFlashMessage, fetchFlashMessages} from '../utils/FlashMessagesUtil.js'

class Home extends React.Component {
    constructor() {
        super();
        this.state = {
            flashMessages: [],
            flashMessagesFetching: false,
        };

        this.addFlashMessage = addFlashMessage.bind(this)
        this.fetchFlashMessages = fetchFlashMessages.bind(this)
    }

    componentDidMount() {
        document.title = Constants.SITE_NAME + ' | Tenant Reviews of Lettings Agents and Properties';

        const getScrollPosition = (el = window) => ({
            x: el.pageXOffset !== undefined ? el.pageXOffset : el.scrollLeft,
            y: el.pageYOffset !== undefined ? el.pageYOffset : el.scrollTop
        });

        const classScrolledDown = 'include-search';

        window.onscroll = function () {
            let pos = getScrollPosition(window);
            let headerNavbar = $('#header-navbar');
            if (pos.y >= 340) {
                if (!headerNavbar.hasClass(classScrolledDown)) {
                    headerNavbar.addClass(classScrolledDown, 500);
                }
            } else if (headerNavbar.hasClass(classScrolledDown)) {
                headerNavbar.removeClass(classScrolledDown, 500);
            }
        };

        this.fetchFlashMessages();
    }

    render() {
        return (
            <Fragment>
                <Row id="home-background" className="no-gutters w-100">
                    <Header className="bg-gradient-primary fixed-top" />
                    <Col id="home" className="align-self-center text-center mt-7 mb-5">

                        <Container className="p-0">
                            <FlashMessages messages={this.state.flashMessages} />
                        </Container>

                        <Container className="mobile-only bg-light-translucent-90 p-4 mb-4">
                            <Form>
                                <FormGroup>
                                    <Label for="propertySearch">Find tenant reviews for<br />properties and lettings agents</Label>
                                    <PropertyAutocomplete
                                        appendSearchIcon={true}
                                        placeholder="Start typing an address..."
                                        inputId="property-autocomplete-mobile"
                                        className="property-autocomplete-home"
                                    />
                                </FormGroup>
                                <p>
                                    After you've entered a few characters,<br />you will see suggested results
                                </p>
                            </Form>
                        </Container>

                        <Container className="desktop-only rounded-lg bg-light-translucent-90 p-5 mt-5 mb-5">
                            <h1 className="logo logo-large"><TextLogo /></h1>
                            <Form>
                                <FormGroup>
                                    <Label for="propertySearch">Find tenant reviews for properties and lettings agents</Label>
                                    <PropertyAutocomplete
                                        appendSearchIcon={true}
                                        inputId="property-autocomplete-desktop"
                                        className="property-autocomplete-home"
                                    />
                                </FormGroup>
                                <p>
                                    After you've entered a few characters, you will see suggested results
                                </p>
                            </Form>
                        </Container>
                    </Col>
                </Row>
            </Fragment>
        );
    }
}

export default Home;
