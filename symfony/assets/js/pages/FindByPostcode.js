import React, {Fragment} from 'react';

import {Button, FormText} from "reactstrap";
import {AvForm, AvGroup, AvInput, AvFeedback} from 'availity-reactstrap-validation';
import Constants from "../Constants";
import Address from "../components/Address";
import LoadingSpinner from "../components/LoadingSpinner";

import '../../styles/find-by-postcode.scss';

class FindByPostcode extends React.Component {

    constructor(props) {
        super(props);
        this.state = {
            postcode: '',
            properties: [],
            isLoading: false,
            loaded: false,
        };

        this.handleAddressClick = this.handleAddressClick.bind(this);
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
            <div className="find-by-postcode">
                <h1>Find an address by postcode</h1>
                <p>
                    To see all property addresses for a postcode, simply enter the full postcode in the search box
                    below, then click <i>Search</i>
                </p>
                <AvForm className="find-by-postcode-form" onValidSubmit={this.handleValidSubmit}>
                    <AvGroup>
                        <AvInput
                            type="text"
                            name="postcode"
                            required
                            onChange={this.handleChange}
                            placeholder="Enter a postcode"
                            value={this.state.email}
                        />
                        <AvFeedback>Please enter a valid UK postcode.</AvFeedback>
                        <FormText>
                            Example: <i>CB4 3LF</i>
                        </FormText>
                    </AvGroup>
                    <Button id="find-by-postcode-submit" color="primary" size="lg" className="mt-1">
                        Search
                    </Button>
                </AvForm>
                {this.state.isLoading &&
                    <LoadingSpinner className="mt-3"/>
                }
                {this.state.loaded &&
                <Fragment>
                    <h2 className="mt-3">{this.state.properties.length} results found in {this.state.postcode}</h2>
                    {this.state.properties.map(
                        ({addressLine1, addressLine2, addressLine3, city, postcode}) => (
                            <Address
                                key={addressLine1}
                                addressLine1={addressLine1}
                                addressLine2={addressLine2}
                                addressLine3={addressLine3}
                                city={city}
                                postcode={postcode}
                                handleClick={this.handleAddressClick}
                            />
                        )
                    )}
                </Fragment>
                }
            </div>
        );
    }

    handleValidSubmit() {
        this.setState({isLoading: true});
        let payload = {
            postcode: this.state.postcode,
            captchaToken: null
        };

        let component = this;
        grecaptcha.ready(function () {
            grecaptcha.execute(Constants.GOOGLE_RECAPTCHA_SITE_KEY, {action: 'submit'}).then(function (captchaToken) {
                payload.captchaToken = captchaToken;
                fetch('/api/postcode', {
                    method: 'POST',
                    body: JSON.stringify(payload),
                })
                    .then(
                        response => {
                            component.setState({isLoading: false});
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
                        component.props.fetchFlashMessages();
                        component.setState({
                            properties: data.vendorProperties,
                            loaded: true,
                        });
                    })
                    .catch(err => console.error("Error:", err));
            });
        });
    }

    handleAddressClick(addressLine1) {
        fetch('/api/property/lookup-slug-from-address?addressLine1=' + addressLine1 + '&postcode=' + this.state.postcode)
            .then(response => response.json())
            .then(data => {
                this.setState({redirectToUrl: '/property/' + data.slug})
            });
    }
}

export default FindByPostcode;