import React from 'react';

import {
    Button, Label,
} from "reactstrap";
import { AvForm, AvGroup, AvInput, AvFeedback } from 'availity-reactstrap-validation';
import LoadingOverlay from "react-loading-overlay";
import Loader from "react-loaders";
import {Redirect} from "react-router-dom";
import Constants from "../Constants";

class ContactForm extends React.Component {

    constructor(props) {
        super(props);
        this.state = {
            email: '',
            name: '',
            message: '',
            agreeTerms: false,
            isFormSubmitting: false,
            redirectToUrl: null,
        };

        this.handleChange = this.handleChange.bind(this);
        this.handleValidSubmit = this.handleValidSubmit.bind(this);
    }

    handleChange(event) {
        const target = event.target;
        const value = target.type === 'checkbox' ? target.checked : target.value;
        const name = target.name;

        this.setState({
            [name]: value
        });
    }

    render() {
        if (this.state.redirectToUrl) {
            return (<Redirect to={this.state.redirectToUrl} />);
        }
        return (
            <LoadingOverlay
                active={this.state.isFormSubmitting}
                styles={{
                    overlay: (base) => ({
                        ...base,
                        background: "#fff",
                        opacity: 0.5,
                    }),
                }}
                spinner={<Loader active type='ball-triangle-path' />}
            >
                <AvForm id="contact-form" onValidSubmit={this.handleValidSubmit} ref={c => (this.form = c)}>
                    <AvGroup>
                        <Label for="email">Your email</Label>
                        <AvInput
                            type="email"
                            name="email"
                            required
                            onChange={this.handleChange}
                            placeholder="Enter your email"
                            value={this.state.email}
                        />
                        <AvFeedback>Please enter a valid email address, example: jane.doe@gmail.com</AvFeedback>
                    </AvGroup>
                    <AvGroup>
                        <Label for="name">Your name</Label>
                        <AvInput
                            type="text"
                            name="name"
                            required
                            onChange={this.handleChange}
                            placeholder="Enter your name"
                            value={this.state.name}
                        />
                        <AvFeedback>Please enter your name.</AvFeedback>
                    </AvGroup>
                    <AvGroup>
                        <Label for="message">Your message</Label>
                        <AvInput
                            type="textarea"
                            name="message"
                            required
                            onChange={this.handleChange}
                            placeholder="Enter your message"
                            value={this.state.message}
                        />
                        <AvFeedback>Please enter your message.</AvFeedback>
                    </AvGroup>
                    <Button id="register-form-submit" color="primary" size="lg" className="mt-4">
                        Contact us
                    </Button>
                </AvForm>
            </LoadingOverlay>
        );
    }

    handleValidSubmit() {
        this.setState({isFormSubmitting: true});
        let payload = {
            email: this.state.email,
            name: this.state.name,
            message: this.state.message,
            captchaToken: null
        };

        let component = this;
        grecaptcha.ready(function() {
            grecaptcha.execute(Constants.GOOGLE_RECAPTCHA_SITE_KEY, {action: 'submit'}).then(function(captchaToken) {
                payload.captchaToken = captchaToken;
                fetch('/api/contact', {
                    method: 'POST',
                    body: JSON.stringify(payload),
                })
                    .then(
                        response => {
                            component.setState({isFormSubmitting: false});
                            if (!response.ok) {
                                if (response.status === 500) {
                                    component.props.addFlashMessage('error', 'Sorry, something went wrong with your request.')
                                }
                                component.props.fetchFlashMessages();
                                return Promise.reject('Error: ' + response.status)
                            }
                            return response.json()
                        }
                    )
                    .then((data) => {
                        component.clearForm();
                        component.setState({redirectToUrl: '/'})
                        component.props.fetchFlashMessages();
                    })
                    .catch(err => console.error("Error:", err));
            });
        });
    }
    clearForm() {
        this.form && this.form.reset();
    }
}

export default ContactForm;