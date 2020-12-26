import React from 'react';

import {
    Button, Form, FormGroup, FormText, Input, Label,
} from "reactstrap";
import Rating from "react-rating";
import {FontAwesomeIcon} from "@fortawesome/react-fontawesome";
import {faStar} from "@fortawesome/free-solid-svg-icons";
import Constants from "../Constants";

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
            propertyStars: null
        };
        this.handleChange = this.handleChange.bind(this);
        this.handleSubmit = this.handleSubmit.bind(this);
        this.handleOverallStarsChange = this.handleOverallStarsChange.bind(this);
        this.handleAgencyStarsChange = this.handleAgencyStarsChange.bind(this);
        this.handleLandlordStarsChange = this.handleLandlordStarsChange.bind(this);
        this.handlePropertyStarsChange = this.handlePropertyStarsChange.bind(this);
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
            <Form onSubmit={this.handleSubmit}>
                <FormGroup>
                    <Label for="reviewerEmail">Your email</Label>
                    <Input
                        type="email"
                        name="reviewerEmail"
                        required
                        onChange={this.handleChange}
                        placeholder="Enter your email"
                    />
                    <FormText>
                        We won't publish this, and will only use it if we need to contact you.
                    </FormText>
                </FormGroup>
                <FormGroup>
                    <Label for="reviewerName">Your name</Label>
                    <Input
                        type="text"
                        name="reviewerName"
                        required
                        onChange={this.handleChange}
                        placeholder="Enter your name"
                    />
                    <FormText>
                        We will publish this. It doesn't have to be your full name if you don't want.
                    </FormText>
                </FormGroup>
                <FormGroup>
                    <Label for="agencyName">Agency company name</Label>
                    <Input
                        type="text"
                        name="agencyName"
                        onChange={this.handleChange}
                        placeholder="Enter agency company"
                    />
                    <FormText>
                        Example: Winkworth. Leave blank if unknown or private landlord.
                    </FormText>
                </FormGroup>
                <FormGroup>
                    <Label for="agencyBranch">Agency branch location</Label>
                    <Input
                        type="text"
                        name="agencyBranch"
                        onChange={this.handleChange}
                        placeholder="Enter agency branch"
                    />
                    <FormText>
                        Example: Coventry. Leave blank if unknown or private landlord.
                    </FormText>
                </FormGroup>
                <FormGroup>
                    <Label for="reviewTitle">Review title</Label>
                    <Input
                        type="text"
                        name="reviewTitle"
                        required
                        onChange={this.handleChange}
                        placeholder="Enter review title"
                    />
                    <FormText>
                        A short sentence or two summarising your experience.
                    </FormText>
                </FormGroup>
                <FormGroup>
                    <Label for="reviewContent">Your review</Label>
                    <Input
                        type="textarea"
                        name="reviewContent"
                        required
                        onChange={this.handleChange}
                        placeholder="Enter your review"
                    />
                    <FormText>
                        Tell us all about your experience with the property, letting agent, landlord etc.
                    </FormText>
                </FormGroup>
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
                <Button color="primary" onClick={this.handleSubmit}>
                    Share your tenancy review
                </Button>
            </Form>
        );
    }

    handleSubmit() {
        let payload = {
            ...this.state, ...{captchaToken: null}
        };
        grecaptcha.ready(function() {
            grecaptcha.execute(Constants.GOOGLE_RECAPTCHA_SITE_KEY, {action: 'submit'}).then(function(captchaToken) {
                payload.captchaToken = captchaToken;
                fetch(`/api/submit-review`, {
                    method: 'POST',
                    body: JSON.stringify(payload),
                    headers: {
                        'Content-Type': 'application/json'
                    }
                }).then(res => res.json())
                    .then(location.reload())
                    .catch(err => console.error("Error:", err));
            });
        });
        event.preventDefault();
    }
}

export default ReviewTenancyForm;