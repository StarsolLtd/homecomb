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
                        <li className="nav-item dropdown"><Link to="/about">About</Link></li>
                        <li className="nav-item dropdown"><Link to="/#how-it-works">How it Works</Link></li>
                        <li className="nav-item dropdown">
                            <a className="dropdown-toggle" href="#" id="navbarDropdown" role="button"
                               data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                Popular Locations
                            </a>
                            <div className="dropdown-menu bg-secondary" aria-labelledby="navbarDropdown">
                                <a className="dropdown-item" href="/l/clerkenwell">Clerkenwell</a>
                                <a className="dropdown-item" href="/l/shoreditch">Shoreditch</a>
                                <div className="dropdown-divider"></div>
                                <a className="dropdown-item" href="/l/cambridge">Cambridge</a>
                                <a className="dropdown-item" href="/l/norwich">Norwich</a>
                            </div>
                        </li>
                    </ul>
                </Collapse>
            </Container>
        </Nav>
    );
}

export default Header;