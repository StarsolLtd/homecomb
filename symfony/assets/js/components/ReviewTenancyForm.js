import React from 'react';

import {
    Button, FormGroup, FormText, Label,
} from "reactstrap";
import { AvForm, AvGroup, AvInput, AvFeedback } from 'availity-reactstrap-validation';
import Rating from "react-rating";
import {FontAwesomeIcon} from "@fortawesome/react-fontawesome";
import {faStar} from "@fortawesome/free-solid-svg-icons";
import Constants from "../Constants";
import LoadingOverlay from "react-loading-overlay";
import Loader from "react-loaders";

class ReviewTenancyForm extends React.Component {

    constructor(props) {
        super(props);
        this.state = {
            propertySlug: this.props.propertySlug,
            reviewerEmail: '',
            reviewerName: '',
            agencyName: '',
            agencyBranch: '',
            reviewTitle: '',
            reviewContent: '',
            overallStars: null,
            landlordStars: null,
            agencyStars: null,
            propertyStars: null,
            formSubmissionInProgress: false,
            user: null
        };
        this.handleChange = this.handleChange.bind(this);
        this.handleValidSubmit = this.handleValidSubmit.bind(this);
        this.handleOverallStarsChange = this.handleOverallStarsChange.bind(this);
        this.handleAgencyStarsChange = this.handleAgencyStarsChange.bind(this);
        this.handleLandlordStarsChange = this.handleLandlordStarsChange.bind(this);
        this.handlePropertyStarsChange = this.handlePropertyStarsChange.bind(this);
    }

    componentDidMount() {
        this.fetchUserData();
    }

    handleChange(event) {
        const target = event.target;
        const value = target.type === 'checkbox' ? target.checked : target.value;
        const name = target.name;

        this.setState({
            [name]: value
        });
    }

    handleOverallStarsChange(value) {
        this.setState({overallStars: value});
    }

    handleAgencyStarsChange(value) {
        this.setState({agencyStars: value});
    }

    handleLandlordStarsChange(value) {
        this.setState({landlordStars: value});
    }

    handlePropertyStarsChange(value) {
        this.setState({propertyStars: value});
    }

    render() {
        return (
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
                    {this.state.user &&
                    <AvGroup>
                        <Label for="reviewerEmail">Your email/username</Label>
                        <AvInput
                            type="text"
                            disabled
                            name="reviewerEmail"
                            value={this.state.user.username}
                        />
                        <FormText>
                            We won't publish this, and will only use it if we need to contact you.
                            If you would like to add this review as a different user, please <a href="/logout">log out</a>.
                        </FormText>
                    </AvGroup>
                    }
                    {!this.state.user &&
                    <AvGroup>
                        <Label for="reviewerEmail">Your email</Label>
                        <AvInput
                            type="email"
                            name="reviewerEmail"
                            required
                            onChange={this.handleChange}
                            placeholder="Enter your email"
                        />
                        <AvFeedback>Please enter a valid email address, example: jane.doe@gmail.com</AvFeedback>
                        <FormText>
                            We won't publish this, and will only use it if we need to contact you.
                        </FormText>
                    </AvGroup>
                    }
                    <AvGroup>
                        <Label for="reviewerName">Your name</Label>
                        <AvInput
                            type="text"
                            name="reviewerName"
                            required
                            onChange={this.handleChange}
                            placeholder="Enter your name"
                        />
                        <AvFeedback>Please enter your name here. Remember we will publish this, so it doesn't have to be your full name if you don't want.</AvFeedback>
                        <FormText>
                            We will publish this. It doesn't have to be your full name if you don't want.
                        </FormText>
                    </AvGroup>
                    <AvGroup>
                        <Label for="agencyName">Agency company name</Label>
                        <AvInput
                            type="text"
                            name="agencyName"
                            onChange={this.handleChange}
                            placeholder="Enter agency company"
                        />
                        <FormText>
                            Example: Winkworth. Leave blank if unknown or private landlord.
                        </FormText>
                    </AvGroup>
                    <AvGroup>
                        <Label for="agencyBranch">Agency branch location</Label>
                        <AvInput
                            type="text"
                            name="agencyBranch"
                            onChange={this.handleChange}
                            placeholder="Enter agency branch"
                        />
                        <FormText>
                            Example: Coventry. Leave blank if unknown or private landlord.
                        </FormText>
                    </AvGroup>
                    <AvGroup>
                        <Label for="reviewTitle">Review title</Label>
                        <AvInput
                            type="text"
                            name="reviewTitle"
                            required
                            onChange={this.handleChange}
                            placeholder="Enter review title"
                        />
                        <AvFeedback>Please enter a short title for your review.</AvFeedback>
                        <FormText>
                            A short sentence or two summarising your experience.
                        </FormText>
                    </AvGroup>
                    <AvGroup>
                        <Label for="reviewContent">Your review</Label>
                        <AvInput
                            type="textarea"
                            name="reviewContent"
                            required
                            onChange={this.handleChange}
                            placeholder="Enter your review"
                        />
                        <AvFeedback>Please your review.</AvFeedback>
                        <FormText>
                            Tell us all about your experience with the property, letting agent, landlord etc.
                        </FormText>
                    </AvGroup>
                    <hr />
                    <h2>Star ratings</h2>
                    <FormGroup>
                        <Label for="overallStars">Overall</Label><br />
                        <Rating
                            name="overallStars"
                            onChange={this.handleOverallStarsChange}
                            initialRating={this.state.overallStars}
                            emptySymbol={
                                <span className="text-rating-unchecked">
                                <FontAwesomeIcon size="lg" icon={faStar} />
                            </span>
                            }
                            fullSymbol={
                                <span className="text-rating">
                                <FontAwesomeIcon size="lg" icon={faStar} />
                            </span>
                            }
                        />
                        <FormText>
                            Rate your overall tenant experience.
                        </FormText>
                    </FormGroup>
                    <FormGroup>
                        <Label for="agencyStars">Agency</Label><br />
                        <Rating
                            name="agencyStars"
                            onChange={this.handleAgencyStarsChange}
                            initialRating={this.state.agencyStars}
                            emptySymbol={
                                <span className="text-rating-unchecked">
                            <FontAwesomeIcon size="lg" icon={faStar} />
                        </span>
                            }
                            fullSymbol={
                                <span className="text-rating">
                            <FontAwesomeIcon size="lg" icon={faStar} />
                        </span>
                            }
                        />
                        <FormText>
                            Rate the agency that managed your tenancy, leave blank if there was no agency.
                        </FormText>
                    </FormGroup>
                    <FormGroup>
                        <Label for="landlordStars">Landlord</Label><br />
                        <Rating
                            name="landlordStars"
                            onChange={this.handleLandlordStarsChange}
                            initialRating={this.state.landlordStars}
                            emptySymbol={
                                <span className="text-rating-unchecked">
                                <FontAwesomeIcon size="lg" icon={faStar} />
                            </span>
                            }
                            fullSymbol={
                                <span className="text-rating">
                                <FontAwesomeIcon size="lg" icon={faStar} />
                            </span>
                            }
                        />
                        <FormText>
                            Rate the landlord, leave blank if you never dealt with them.
                        </FormText>
                    </FormGroup>
                    <FormGroup>
                        <Label for="propertyStars">Property</Label><br />
                        <Rating
                            name="propertyStars"
                            onChange={this.handlePropertyStarsChange}
                            initialRating={this.state.propertyStars}
                            emptySymbol={
                                <span className="text-rating-unchecked">
                                <FontAwesomeIcon size="lg" icon={faStar} />
                            </span>
                            }
                            fullSymbol={
                                <span className="text-rating">
                                <FontAwesomeIcon size="lg" icon={faStar} />
                            </span>
                            }
                        />
                        <FormText>
                            Rate the property itself.
                        </FormText>
                    </FormGroup>
                    <Button color="primary">
                        Share your tenancy review
                    </Button>
                </AvForm>
            </LoadingOverlay>
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
                fetch(`/api/submit-review`, {
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

    fetchUserData() {
        fetch(
            '/api/user',
            {
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                }
            }
        )
            .then((response) => {
                if (!response.ok) throw new Error(response.status);
                else return response.json();
            })
            .then(data => {
                this.setState({
                    user: data
                });
            })
            .catch(err => console.error("Error:", err));
    }
}

export default ReviewTenancyForm;