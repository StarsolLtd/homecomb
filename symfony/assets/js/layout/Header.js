import React from "react";
import {Link} from "react-router-dom";
import {Container, Nav} from "reactstrap";
import TextLogo from "../components/TextLogo";

function Header() {
    return (
        <Nav className="navbar navbar-dark navbar-header light-bronze">
            <Container>
                <span className="navbar-brand logo-medium">
                    <Link to="/">
                        <TextLogo />
                    </Link>
                </span>
            </Container>
        </Nav>
    );
}

export default Header;