import React from 'react';
import {Label, FormText, Button, Container} from 'reactstrap';
import DataLoader from "../../components/DataLoader";
import LoadingOverlay from "react-loading-overlay";
import Loader from "react-loaders";
import {AvForm, AvGroup, AvInput} from "availity-reactstrap-validation";
import Constants from "../../Constants";
import FlashMessages from "../../layout/FlashMessages";
import UpdateAgency from "./UpdateAgency";

class View extends React.Component {
    constructor(props) {
        super(props);
        this.state = {
            formSubmissionInProgress: false,
            flashMessages: [],
        };

        this.addFlashMessage = this.addFlashMessage.bind(this);
        this.submit = this.submit.bind(this);
    }

    render() {
        const Content = this.props.content;

        return (
            <Container>
                <FlashMessages messages={this.state.flashMessages} />
                <Content
                    addFlashMessage={this.addFlashMessage}
                    formSubmissionInProgress={this.state.formSubmissionInProgress}
                    submit={this.submit}
                />
            </Container>
        );
    }

    addFlashMessage(context, content) {
        this.setState({ flashMessages: [...this.state.flashMessages, {key: Date.now(), context, content}] })
    }

    submit(payload, url, method, successMessage) {
        this.setState({formSubmissionInProgress: true});

        let component = this;
        grecaptcha.ready(function() {
            grecaptcha.execute(Constants.GOOGLE_RECAPTCHA_SITE_KEY, {action: 'submit'}).then(function(captchaToken) {
                payload.captchaToken = captchaToken;
                fetch(url, {method: method, body: JSON.stringify(payload)})
                    .then((response) => {
                        component.setState({formSubmissionInProgress: false});
                        if (!response.ok) throw new Error(response.status);
                        else return response.json();
                    })
                    .then((data) => {
                        component.addFlashMessage('success', successMessage)
                    })
                    .catch(err => console.error("Error:", err));
            });
        });
    }
}

export default View;