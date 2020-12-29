import React from 'react';
import {Label, Button, FormText, Container} from 'reactstrap';
import LoadingOverlay from "react-loading-overlay";
import { AvForm, AvGroup, AvInput, AvFeedback } from 'availity-reactstrap-validation';
import Constants from "../../Constants";
import Loader from "react-loaders";

class CreateAgency extends React.Component {
    constructor() {
        super();
        this.state = {
            agencyName: '',
            externalUrl: '',
            postcode: '',
            captchaToken: '',
            formSubmissionInProgress: false,
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
        return (
            <Container>
                <LoadingOverlay
                    active={this.state.formSubmissionInProgress}
                    styles={{
                        overlay: (base) => ({
                            ...base,
                            background: "#fff",
                            opacity: 0.5,
                        }),
                    }}
                    spinner={<Loader active type='ball-triangle-path' />}
                >
                    <AvForm onValidSubmit={this.handleValidSubmit}>
                        <AvGroup>
                            <Label for="agencyName">Agency name</Label>
                            <AvInput name="agencyName" required onChange={this.handleChange} />
                            <AvFeedback>Please enter your agency name.</AvFeedback>
                            <FormText>
                                Please enter the trading name of your agency. Example: Cambridge Lettings.
                            </FormText>
                        </AvGroup>
                        <AvGroup>
                            <Label for="externalUrl">Website URL</Label>
                            <AvInput name="externalUrl" type="url" placeholder="http://yoursite.com" onChange={this.handleChange} />
                            <FormText>
                                Optional. If your agency has a website, enter its URL here. Example: http://www.cambridgelettings.com/
                            </FormText>
                        </AvGroup>
                        <AvGroup>
                            <Label for="postcode">Postcode</Label>
                            <AvInput name="postcode" onChange={this.handleChange} />
                            <FormText>
                                Optional. Please enter the postcode of your agency's primary office.
                            </FormText>
                        </AvGroup>
                        <Button color="primary">
                            Create your agency
                        </Button>
                    </AvForm>
                </LoadingOverlay>
            </Container>
        );
    }

    handleValidSubmit() {
        this.setState({formSubmissionInProgress: true});
        let payload = {
            ...this.state, ...{captchaToken: null}
        };
        let component = this;
        grecaptcha.ready(function() {
            grecaptcha.execute(Constants.GOOGLE_RECAPTCHA_SITE_KEY, {action: 'submit'}).then(function(captchaToken) {
                payload.captchaToken = captchaToken;
                fetch(`/api/verified/agency`, {
                    method: 'POST',
                    body: JSON.stringify(payload),
                    headers: {
                        'Content-Type': 'application/json'
                    }
                })
                    .then((response) => {
                        component.setState({formSubmissionInProgress: false});
                        if (!response.ok) throw new Error(response.status);
                        else return response.json();
                    })
                    .then((data) => {
                        location.reload()
                    })
                    .catch(err => console.error("Error:", err));
            });
        });
    }
}

export default CreateAgency;
