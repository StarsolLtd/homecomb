import React from "react";
import {Link} from "react-router-dom";
import {Container, Nav} from "reactstrap";
import TextLogo from "../components/TextLogo";


const Header = (props) => {
    const navClasses = `navbar navbar-dark navbar-header light-bronze ${props.className}`;
    return (
        <div className={navClasses}>
            <Container>
                <span className="navbar-brand logo-medium">
                    <Link to="/">
                        <TextLogo className={props.textLogoClassName}/>
                    </Link>
                </span>
            </Container>
        </div>
    );
}

export default Header;