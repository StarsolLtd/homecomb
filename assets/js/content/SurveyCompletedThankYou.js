import React from "react";
import {Alert} from "reactstrap";
import Constants from "../Constants";

const SurveyCompletedThankYou = () => {
    return (
        <Alert color="success" className="p-4 text-center alert-success alert-dismissible fade show survey-completed-thank-you">
            <h2>Survey complete!</h2>

            <p>
                Thank you! We have received your answers and will use them to make {Constants.SITE_NAME} even better!
            </p>

            <button type="button" className="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </Alert>
    );
}

export default SurveyCompletedThankYou;