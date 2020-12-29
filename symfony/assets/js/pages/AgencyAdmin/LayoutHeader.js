import React from "react";
import {Link} from "react-router-dom";
import {Col, Row} from "reactstrap";

function LayoutHeader() {
    return (
        <Row>
            <Col md={2} className="light-bronze p-1 pl-4">
                <span className="navbar-brand logo-sm">
                    <span className="red">Home</span><span className="bronze">Comb</span>
                </span>
            </Col>
            <Col md={10} className="light-bronze">

            </Col>
        </Row>
    );
}

export default LayoutHeader;