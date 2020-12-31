import React from "react";
import {Link} from "react-router-dom";
import {Collapse, Container, Nav} from "reactstrap";
import TextLogo from "../components/TextLogo";

const Header = (props) => {
    const navClasses = `w-100 navbar navbar-expand-md navbar-dark navbar-header ${props.className}`;
    return (
        <Nav id="header-navbar" className={navClasses}>
            <Container>
                <span className="navbar-brand logo-medium mr-5">
                    <Link to="/">
                        <TextLogo className="logo-white"/>
                    </Link>
                </span>

                <button className="navbar-toggler" type="button" data-toggle="collapse" data-target="#collapsibleNavbar">
                    <span className="navbar-toggler-icon" />
                </button>

                <Collapse className="navbar-collapse" id="collapsibleNavbar">
                    <ul className="navbar-nav">
                        <li><Link to="/about">About</Link></li>
                        <li><Link to="/#how-it-works">How it Works</Link></li>
                    </ul>
                </Collapse>
            </Container>
        </Nav>
    );
}

export default Header;