import React from "react";
import {Button, Col, Container} from "reactstrap";
import Constants from "../Constants";

import '../../styles/how-it-works.scss';


const HowItWorks = (props) => {
    return (
        <div className="how-it-works">
            <Container>
                <Col md={12} className="p-4 text-center">
                    <h2>How {Constants.SITE_NAME} Works</h2>

                    <p>
                        {Constants.SITE_NAME} allows tenants to review their tenancies at properties in the United Kingdom
                    </p>

                    <p>
                        We encourage tenants to review all factors in their tenancy,<br />
                        not only the property itself, but also the landlord and lettings agents.
                    </p>

                    <p>
                        We compile lists of the best rated agents in various locations around the country.<br />
                        This allows us to shows the best rated lettings agents in your area.
                    </p>


                    <Button size="lg" color="primary">
                        Review your tenancy
                    </Button>
                </Col>
            </Container>
        </div>
    );
}

export default HowItWorks;