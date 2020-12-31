import React from "react";
import {Link} from "react-router-dom";
import {Container, Nav} from "reactstrap";
import TextLogo from "../components/TextLogo";


const Header = (props) => {
    const navClasses = `bg-gradient-primary navbar navbar-dark navbar-header ${props.className}`;
    return (
        <Nav className={navClasses}>
            <Container>
                <span className="navbar-brand logo-medium">
                    <Link to="/">
                        <TextLogo className="logo-white"/>
                    </Link>
                </span>
            </Container>
        </Nav>
    );
}

export default Header;