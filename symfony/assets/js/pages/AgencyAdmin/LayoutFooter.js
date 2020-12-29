import React from "react";
import {Link} from "react-router-dom";
import {Col, Nav, Row} from "reactstrap";

const LayoutFooter = (props) => {
    return (
        <Row>
            <Col md={3} className="light-bronze">

            </Col>
            <Col md={9} className="mt-auto light-bronze">
                <ul className="list-inline text-center">
                    {props.user &&
                    <li className="list-inline-item"><a href="/logout" className="nav-link">Log Out</a></li>
                    }
                    {!props.user &&
                    <li className="list-inline-item"><a href="/login" className="nav-link">Log In</a></li>
                    }
                </ul>
            </Col>
        </Row>
    );
}

export default LayoutFooter;