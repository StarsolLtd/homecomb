import React from 'react';

import {Button} from "reactstrap";

import Constants from "../Constants";
import {faThumbsUp} from "@fortawesome/free-solid-svg-icons";
import {FontAwesomeIcon} from "@fortawesome/react-fontawesome";

class Vote extends React.Component {

    constructor(props) {
        super(props);
        this.state = {
            isSubmitting: false
        };

        this.handleVote = this.handleVote.bind(this);
    }

    render() {
        return (
            <Button onClick={this.handleVote} className={'btn-light ' + this.props.className}>
                <FontAwesomeIcon icon={faThumbsUp} className="text-primary" /> {this.props.positiveTerm}
            </Button>
        );
    }

    handleVote() {
        this.setState({isSubmitting: true});
        let payload = {
            entityId: this.props.entityId,
            entityName: this.props.entityName,
            positive: true,
            captchaToken: null,
        };
        let component = this;
        grecaptcha.ready(function() {
            grecaptcha.execute(Constants.GOOGLE_RECAPTCHA_SITE_KEY, {action: 'submit'}).then(function(captchaToken) {
                payload.captchaToken = captchaToken;
                fetch('/api/vote', {method: 'POST', body: JSON.stringify(payload)})
                    .then(
                        response => {
                            component.setState({isSubmitting: false});
                            if (!response.ok) {
                                if (response.status === 401) {
                                    location.href = '/login';
                                }
                                if (response.status === 500) {
                                    // TODO
                                }
                                return Promise.reject('Error: ' + response.status)
                            }
                            return response.json()
                        }
                    )
                    .then((data) => {
                        // TODO
                    })
                    .catch(err => console.error("Error:", err));
            });
        });
    }
}

export default Vote;