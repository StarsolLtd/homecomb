import React, {Fragment} from 'react';
import {Container, Label, Button, FormText} from 'reactstrap';
import LoadingOverlay from "react-loading-overlay";
import { AvForm, AvGroup, AvInput, AvFeedback } from 'availity-reactstrap-validation';
import Constants from "../Constants";
import Loader from "react-loaders";
import InputProperty from "../components/InputProperty";
import LoadingSpinner from "../components/LoadingSpinner";

class CreateReviewSolicitation extends React.Component {
    constructor() {
        super();
        this.state = {
            agency: null,
            branches: [],
            branchSlug: '',
            propertySlug: '',
            recipientTitle: '',
            recipientFirstName: '',
            recipientLastName: '',
            recipientEmail: '',
            captchaToken: '',
            loading: false,
            formSubmissionInProgress: false,
        };
        this.handleChange = this.handleChange.bind(this);
        this.handleValidSubmit = this.handleValidSubmit.bind(this);
        this.setPropertySlugState = this.setPropertySlugState.bind(this);
    }

    handleChange(event) {

        const target = event.target;
        const value = target.type === 'checkbox' ? target.checked : target.value;
        const name = target.name;

        this.setState({
            [name]: value
        });
    }

    componentDidMount() {
        this.fetchData();
    }

    fetchData() {
        this.setState({loading: true});
        fetch(
            '/api/verified/solicit-review',
            {
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                }
            }
        )
            .then(response => response.json())
            .then(data => {
                this.setState({
                    agency: data.agency,
                    branches: data.branches,
                    loading: false,
                    loaded: true
                });
            });
    }

    setPropertySlugState(value) {
        if (typeof value !== "undefined") {
            this.setState({propertySlug: value});
        }
    }

    render() {
        return (
            <Container>
                {this.state.loading &&
                <LoadingSpinner />
                }
                {!this.state.loading && this.state.loaded &&
                <Fragment>
                    <h1>Request a review for {this.state.agency.name}</h1>
                    <p>
                        If you would like to request one of your tenant's review their tenancy with you, please complete
                        the form below. We will send them an email with a unique link allowing them to review.
                    </p>
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
                                <Label for="branchSlug">Branch</Label>
                                <AvInput type="select" name="branchSlug" required onChange={this.handleChange}>
                                    <option value="" disabled>-Please select-</option>
                                    {this.state.branches.map(
                                        ({ slug, name }) => (
                                            <option key={slug} value={slug}>{name}</option>
                                        )
                                    )
                                    }
                                </AvInput>
                                <AvFeedback>Please select a branch.</AvFeedback>
                                <FormText>
                                    Select the branch of your agency by which the tenant was managed.
                                </FormText>
                            </AvGroup>
                            <AvGroup>
                                <Label for="propertySlug">Tenancy property address</Label>
                                <AvInput type="hidden" name="propertySlug" required value={this.state.propertySlug} />
                                <InputProperty
                                    inputId="input-property"
                                    source="/api/property/suggest-property"
                                    placeholder="Start typing a property address..."
                                    setPropertySlugState={this.setPropertySlugState}
                                />
                                <AvFeedback>Please enter a tenancy property address.</AvFeedback>
                                <FormText>
                                    Please start typing the address of the tenancy, then select the correct address when it appears.
                                </FormText>
                            </AvGroup>
                            <AvGroup>
                                <Label for="recipientFirstName">Tenant first name</Label>
                                <AvInput name="recipientFirstName" required onChange={this.handleChange} placeholder="Enter tenant first name" />
                                <AvFeedback>Please enter the tenant's first name.</AvFeedback>
                                <FormText>
                                    Please enter the first name of the tenant. Example: Jane.
                                </FormText>
                            </AvGroup>
                            <AvGroup>
                                <Label for="recipientLastName">Tenant surname</Label>
                                <AvInput name="recipientLastName" required onChange={this.handleChange} placeholder="Enter tenant surname" />
                                <AvFeedback>Please enter the tenant's surname.</AvFeedback>
                                <FormText>
                                    Please enter the surname of the tenant. Example: Smith.
                                </FormText>
                            </AvGroup>
                            <AvGroup>
                                <Label for="recipientEmail">Reviewer email</Label>
                                <AvInput name="recipientEmail" type="email" required onChange={this.handleChange} placeholder="Enter tenant email" />
                                <AvFeedback>Please enter the tenant's email address.</AvFeedback>
                                <FormText>
                                    Please enter the email address of the tenant. Example: jane.smith@domain.com
                                </FormText>
                            </AvGroup>
                            <Button color="primary">
                                Request review
                            </Button>
                        </AvForm>
                    </LoadingOverlay>
                </Fragment>
                }
            </Container>
        );
    }

    handleValidSubmit() {
        this.setState({formSubmissionInProgress: true});
        let payload = {
            branchSlug: this.state.branchSlug,
            propertySlug: this.state.propertySlug,
            recipientTitle: this.state.recipientTitle,
            recipientFirstName: this.state.recipientFirstName,
            recipientLastName: this.state.recipientLastName,
            recipientEmail: this.state.recipientEmail,
            captchaToken: null,
        };
        let component = this;
        grecaptcha.ready(function() {
            grecaptcha.execute(Constants.GOOGLE_RECAPTCHA_SITE_KEY, {action: 'submit'}).then(function(captchaToken) {
                payload.captchaToken = captchaToken;
                fetch(`/api/verified/solicit-review`, {
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

export default CreateReviewSolicitation;
