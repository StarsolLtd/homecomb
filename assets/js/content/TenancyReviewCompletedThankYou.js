import React, {Fragment} from "react";
import {Alert, Button, Col, Row} from "reactstrap";
import {Link} from "react-router-dom";

const TenancyReviewCompletedThankYou = (props) => {
    return (
        <Alert color="success" className="p-4 text-center alert-success alert-dismissible fade show review-completed-thank-you">
            <h2>Thank you!</h2>

            <p>
                Your tenancy review was received successfully and will be checked by our moderation team shortly.
            </p>

            {props.city &&
                <Fragment>
                    <hr />
                    <h5 className="mt-1 mb-4">Would you also like to review <span className="city-name">{props.city.name}</span> generally?</h5>

                    <Link to={'/c/' + props.city.slug}>
                        <Button className="btn btn-primary navigate-to-city-locale-review-form">Yes! Take me to the form</Button>
                    </Link>
                </Fragment>
            }

            <button type="button" className="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </Alert>
    );
}

export default TenancyReviewCompletedThankYou;