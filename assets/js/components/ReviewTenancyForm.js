import React, {Fragment} from 'react';

import {
    Button, FormGroup, FormText, Label,
} from "reactstrap";
import {AvForm, AvGroup, AvInput, AvFeedback, AvCheckboxGroup, AvCheckbox} from 'availity-reactstrap-validation';
import Rating from "react-rating";
import {FontAwesomeIcon} from "@fortawesome/react-fontawesome";
import {faStar} from "@fortawesome/free-solid-svg-icons";
import Constants from "../Constants";
import LoadingOverlay from "react-loading-overlay";
import Loader from "react-loaders";
import ReviewCompletedThankYou from "../content/ReviewCompletedThankYou";
import InputProperty from "./InputProperty";

import DatePicker from "react-datepicker";
import 'react-datepicker/dist/react-datepicker.css';

export default class ReviewTenancyForm extends React.Component {

    constructor(props) {
        super(props);

        let endMinDate = new Date();
        endMinDate.setDate(endMinDate.getDate()-400);
        endMinDate = new Date(endMinDate.getFullYear(), endMinDate.getMonth(), 1)

        let endMaxDate = new Date();
        endMaxDate.setDate(endMaxDate.getDate()+60);
        endMaxDate = new Date(endMaxDate.getFullYear(), endMaxDate.getMonth(), 1)

        this.state = {
            fixedProperty: this.props.hasOwnProperty('fixedProperty') ? this.props.fixedProperty : false,
            propertySlug: this.props.propertySlug,
            reviewerEmail: this.props.reviewerEmail,
            reviewerName: this.props.reviewerName,
            fixedBranch: this.props.hasOwnProperty('fixedBranch') ? this.props.fixedBranch : false,
            agencyName: this.props.agency ? this.props.agency.name : '',
            agencyBranch: this.props.branch ? this.props.branch.name : '',
            reviewTitle: '',
            reviewContent: '',
            start: null,
            end: null,
            endMinDate: endMinDate,
            endMaxDate: endMaxDate,
            overallStars: null,
            landlordStars: null,
            agencyStars: null,
            propertyStars: null,
            agreeTerms: false,
            isFormSubmitting: false,
            completedAndThankYou: false,
            user: null,
            agency: this.props.agency,
            branch: this.props.branch,
            code: this.props.code,
        };
    }

    componentDidMount() {
        this.fetchUserData();
    }

    handleChange = (event) => {
        const target = event.target;
        const value = target.type === 'checkbox' ? target.checked : target.value;
        const name = target.name;

        this.setState({
            [name]: value
        });
    }

    handleStartChange = (value) => {
        this.setState({start: value});
    }

    handleEndChange = (value) => {
        this.setState({end: value});
    }

    handleOverallStarsChange = (value) => {
        this.setState({overallStars: value});
    }

    handleAgencyStarsChange = (value) => {
        this.setState({agencyStars: value});
    }

    handleLandlordStarsChange = (value) => {
        this.setState({landlordStars: value});
    }

    handlePropertyStarsChange = (value) => {
        this.setState({propertyStars: value});
    }

    setPropertySlugState = (value) => {
        if (typeof value !== "undefined") {
            this.setState({propertySlug: value});
        }
    }

    render() {
        if (this.state.completedAndThankYou) {
            return <ReviewCompletedThankYou />;
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
                <AvForm id="review-tenancy-form" onValidSubmit={this.handleValidSubmit} ref={c => (this.form = c)}>
                    {this.state.fixedProperty &&
                        <AvInput type="hidden" name="propertySlug" value={this.state.propertySlug}/>
                    }
                    {!this.state.fixedProperty &&
                    <AvGroup>
                        <Label for="propertySlug">Tenancy property address</Label>
                        <AvInput type="hidden" name="propertySlug" required value={this.state.propertySlug}/>
                        <InputProperty
                            inputId="input-property"
                            source="/api/property/suggest-property"
                            placeholder="Start typing a property address..."
                            setPropertySlugState={this.setPropertySlugState}
                        />
                        <AvFeedback>Please enter a tenancy property address.</AvFeedback>
                        <FormText>
                            Please start typing the address of the property, then select the correct address when it
                            appears.
                        </FormText>
                    </AvGroup>
                    }
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
                            value={this.state.reviewerEmail}
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
                            value={this.state.reviewerName}
                        />
                        <AvFeedback>Please enter your name here. Remember we will publish this, so it doesn't have to be your full name if you don't want.</AvFeedback>
                        <FormText>
                            We will publish this. It doesn't have to be your full name if you don't want.
                        </FormText>
                    </AvGroup>
                    <FormGroup>
                        <Label for="start">Tenancy start month/year</Label>
                        <div>
                            <DatePicker
                                onChange={this.handleStartChange}
                                dateFormat="MMMM yyyy"
                                showMonthYearPicker
                                selected={this.state.start}
                                placeholderText="Select tenancy start month and year"
                                maxDate={new Date()}
                                className="form-control"
                            />
                            <FormText>
                                The month you began living at the address.
                            </FormText>
                        </div>
                    </FormGroup>
                    <FormGroup>
                        <Label for="end">Tenancy end month/year</Label>
                        <div>
                            <DatePicker
                                onChange={this.handleEndChange}
                                dateFormat="MMMM yyyy"
                                showMonthYearPicker
                                selected={this.state.end}
                                minDate={this.state.endMinDate}
                                maxDate={this.state.endMaxDate}
                                placeholderText="Select tenancy end month and year"
                                className="form-control"
                            />
                            <FormText>
                                Leave blank if your tenancy is ongoing.
                            </FormText>
                        </div>
                    </FormGroup>
                    {this.state.fixedBranch &&
                        <AvGroup>
                            <AvInput type="hidden" name="agencyName" value={this.state.agencyName}/>
                            <AvInput type="hidden" name="agencyBranch" value={this.state.agencyBranch}/>
                            <Label for="agencyInfo">Agency</Label>
                            <AvInput
                                type="text"
                                disabled
                                name="agencyInfo"
                                value={this.state.agency.name + ' - ' + this.state.branch.name}
                            />
                            <FormText>
                                We already know the agency and it is displayed here for information purposes.
                                This field can't be edited.
                            </FormText>
                        </AvGroup>
                    }
                    {!this.state.fixedBranch &&
                        <Fragment>
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
                        </Fragment>
                    }
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
                    <hr />
                    <AvCheckboxGroup name="agreeTermsGroup" inline required>
                        <AvCheckbox name="agreeTerms" value="yes" />
                        I agree to the {Constants.SITE_NAME} <a href="/terms" target="_blank">terms and conditions</a>.
                        <AvFeedback>You must agree to the terms and conditions.</AvFeedback>
                    </AvCheckboxGroup>
                    <Button id="review-tenancy-form-submit" color="primary">
                        Share your tenancy review
                    </Button>
                </AvForm>
            </LoadingOverlay>
        );
    }

    handleValidSubmit = () => {
        this.setState({isFormSubmitting: true});

        const start = this.state.start !== null
            ? this.formatDate(this.state.start)
            : null;

        const end = this.state.end !== null
            ? this.formatDate(this.state.end)
            : null;

        let payload = {
            propertySlug: this.state.propertySlug,
            code: this.state.code,
            reviewerName: this.state.reviewerName,
            reviewerEmail: this.state.reviewerEmail,
            agencyName: this.state.agencyName,
            agencyBranch: this.state.agencyBranch,
            reviewTitle: this.state.reviewTitle,
            reviewContent: this.state.reviewContent,
            overallStars: this.state.overallStars,
            agencyStars: this.state.agencyStars,
            landlordStars: this.state.landlordStars,
            propertyStars: this.state.propertyStars,
            start: start,
            end: end,
            captchaToken: null
        };

        let component = this;
        grecaptcha.ready(function() {
            grecaptcha.execute(Constants.GOOGLE_RECAPTCHA_SITE_KEY, {action: 'submit'}).then(function(captchaToken) {
                payload.captchaToken = captchaToken;
                fetch(`/api/submit-review`, {
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
                        component.props.completedThankYou();
                        component.props.fetchFlashMessages();
                    })
                    .catch(err => console.error("Error:", err));
            });
        });
    }

    fetchUserData = () => {
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

    clearForm = () => {
        this.form && this.form.reset();
    }

    formatDate = (date) => {
        let d = new Date(date),
            month = '' + (d.getMonth() + 1),
            day = '' + d.getDate(),
            year = d.getFullYear();

        if (month.length < 2)
            month = '0' + month;
        if (day.length < 2)
            day = '0' + day;

        return [year, month, day].join('-');
    }
}
