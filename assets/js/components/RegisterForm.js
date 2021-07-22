import React from 'react';

import {
    Button, FormText, Label,
} from "reactstrap";
import { AvCheckbox, AvCheckboxGroup, AvForm, AvGroup, AvInput, AvFeedback } from 'availity-reactstrap-validation';
import LoadingOverlay from "react-loading-overlay";
import Loader from "react-loaders";
import {Redirect} from "react-router-dom";
import Constants from "../Constants";

class RegisterForm extends React.Component {

    constructor(props) {
        super(props);
        this.state = {
            email: '',
            firstName: '',
            lastName: '',
            plainPassword: '',
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
                <AvForm id="register-form" onValidSubmit={this.handleValidSubmit} ref={c => (this.form = c)}>
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
                        <FormText>
                            We will send a message to this address for validation purposes.
                        </FormText>
                    </AvGroup>
                    <AvGroup>
                        <Label for="firstName">Your first name</Label>
                        <AvInput
                            type="text"
                            name="firstName"
                            required
                            onChange={this.handleChange}
                            placeholder="Enter your first name"
                            value={this.state.firstName}
                        />
                        <AvFeedback>Please enter your first name.</AvFeedback>
                    </AvGroup>
                    <AvGroup>
                        <Label for="firstName">Your last name</Label>
                        <AvInput
                            type="text"
                            name="lastName"
                            required
                            onChange={this.handleChange}
                            placeholder="Enter your last name"
                            value={this.state.lastName}
                        />
                        <AvFeedback>Please enter your last name.</AvFeedback>
                    </AvGroup>
                    <AvGroup>
                        <Label for="plainPassword">Password</Label>
                        <AvInput
                            type="password"
                            name="plainPassword"
                            required
                            onChange={this.handleChange}
                            placeholder="Enter a password"
                            value={this.state.plainPassword}
                        />
                        <AvFeedback>Please enter a password.</AvFeedback>
                    </AvGroup>
                    <AvCheckboxGroup name="agreeTermsGroup" inline required>
                        <AvCheckbox name="agreeTerms" value="yes" />
                        I agree to the {Constants.SITE_NAME} <a href="/terms" target="_blank">terms and conditions</a>.
                        <AvFeedback>You must agree to the terms and conditions.</AvFeedback>
                    </AvCheckboxGroup>
                    <Button id="register-form-submit" color="primary" size="lg" className="mt-4">
                        Create account
                    </Button>
                </AvForm>
            </LoadingOverlay>
        );
    }

    handleValidSubmit() {
        this.setState({isFormSubmitting: true});
        let payload = {
            email: this.state.email,
            firstName: this.state.firstName,
            lastName: this.state.lastName,
            plainPassword: this.state.plainPassword,
            captchaToken: null
        };

        let component = this;
        grecaptcha.ready(function() {
            grecaptcha.execute(Constants.GOOGLE_RECAPTCHA_SITE_KEY, {action: 'submit'}).then(function(captchaToken) {
                payload.captchaToken = captchaToken;
                fetch('/api/register', {
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

export default RegisterForm;