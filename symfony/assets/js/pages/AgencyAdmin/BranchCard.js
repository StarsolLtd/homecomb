import React from "react";
import {Link} from "react-router-dom";

import {Row, Col, Button, CardHeader, Card, CardFooter} from "reactstrap";

const BranchCard = (props) => {
    return (
        <Col sm="12" md="4">
            <Card className="mb-3 p-3">
                <h3 className="card-header-title font-weight-normal">
                    {props.name}
                </h3>
                <Row className="no-gutters">
                    <Col sm="6" className="p-2">
                        <Link to={'/verified/branch/' + props.slug}>
                            <Button className="btn-icon-vertical btn-transition-text btn-transition btn-secondary"
                                    outline
                            >
                                <i className="lnr-apartment text-dark opacity-7 btn-icon-wrapper mb-2"> {" "} </i>
                                Update Info
                            </Button>
                        </Link>
                    </Col>
                    <Col sm="6" className="p-2">
                        <a href={'/branch/' + props.slug} target="_blank">
                            <Button
                                className="btn-icon-vertical btn-transition-text btn-transition btn-secondary"
                                outline
                            >
                                <i className="lnr-store text-dark opacity-7 btn-icon-wrapper mb-2"> {" "} </i>
                                View
                            </Button>
                        </a>
                    </Col>
                </Row>
            </Card>
        </Col>
    );
}

export default BranchCard;