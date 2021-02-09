import React, {Fragment} from 'react';

import {Button} from "reactstrap";

import Constants from "../Constants";
import {faThumbsUp} from "@fortawesome/free-solid-svg-icons";
import {FontAwesomeIcon} from "@fortawesome/react-fontawesome";

import '../../styles/vote.scss';
import LoadingSpinner from "./LoadingSpinner";

class Vote extends React.Component {

    constructor(props) {
        super(props);
        this.state = {
            hasVoted: false,
            isSubmitting: false,
            positiveVotes: this.props.positiveVotes
        };

        this.handleVote = this.handleVote.bind(this);
    }

    render() {
        let buttonClassName = 'btn-light ' + this.props.className;
        if (this.state.hasVoted) {
            buttonClassName += ' has-voted';
        }
        return (
            <Button onClick={this.handleVote} className={buttonClassName}>
                <FontAwesomeIcon icon={faThumbsUp} className="text-primary" /> {this.props.positiveTerm}
                {this.state.positiveVotes > 0 &&
                <Fragment>
                    {' '}
                    <span className="positive-votes">{this.state.positiveVotes}</span>
                </Fragment>
                }
                {this.state.isSubmitting &&
                <Fragment>
                    {' '}
                    <LoadingSpinner className="loading-spinner-small"/>
                </Fragment>
                }
            </Button>
        );
    }

    handleVote() {
        if (this.state.hasVoted) {
            return;
        }

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
                                return Promise.reject('Error: ' + response.status)
                            }
                            component.setState({hasVoted: true});

                            return response.json()
                        }
                    )
                    .then((data) => {
                        component.setState({positiveVotes: data.positiveVotes});
                    })
                    .catch(err => console.error("Error:", err));
            });
        });
    }
}

export default Vote;