import React from "react";
import {Link} from "react-router-dom";
import {Container, Nav} from "reactstrap";

function Header() {
    return (
        <Nav className="navbar navbar-dark navbar-header light-bronze">
            <Container>
                <span className="navbar-brand logo-medium">
                    <Link to="/">
                        <span className="red">Home</span><span className="bronze">Comb</span>
                    </Link>
                </span>
            </Container>
        </Nav>
    );
}

export default Header;