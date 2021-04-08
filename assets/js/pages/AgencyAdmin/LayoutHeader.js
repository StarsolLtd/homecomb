import React from "react";
import {Link} from "react-router-dom";
import {Button, Collapse, Container, Nav} from "reactstrap";
import TextLogo from "../../components/TextLogo";

const LayoutHeader = (props) => {
    return (
        <Nav className="bg-gradient-primary navbar navbar-expand-md navbar-dark navbar-header">
            <Container>
                <Link to="/verified/dashboard" className="navbar-brand font-weight-bold mr-4">
                    <TextLogo className="logo-white"/>
                </Link>

                <span className="navbar-brand mr-4">
                    Agency Admin Area
                </span>

                <button className="navbar-toggler" type="button" data-toggle="collapse" data-target="#collapsibleNavbar">
                    <span className="navbar-toggler-icon" />
                </button>

                <Collapse className="navbar-collapse" id="collapsibleNavbar">
                    {props.user.agencyAdmin &&
                    <ul className="navbar-nav">
                        <li><Link to="/verified/dashboard"><Button>Dashboard</Button></Link></li>
                        <li><Link to="/verified/agency" className="update-agency-link"><Button>Update Agency</Button></Link></li>
                        <li><Link to="/verified/request-review" className="request-review-link"><Button>Request Review</Button></Link></li>
                    </ul>
                    }
                </Collapse>
            </Container>
        </Nav>
    );
}

export default LayoutHeader;