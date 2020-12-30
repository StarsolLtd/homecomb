import React, {Fragment} from 'react';
import {Label, FormText, Button, Container} from 'reactstrap';
import DataLoader from "../../components/DataLoader";
import LoadingOverlay from "react-loading-overlay";
import Loader from "react-loaders";
import {AvForm, AvGroup, AvInput} from "availity-reactstrap-validation";
import Constants from "../../Constants";

class UpdateAgency extends React.Component {
    constructor(props) {
        super(props);
        this.state = {
            slug: '',
            name: '',
            externalUrl: '',
            postcode: '',
            loaded: false,
            captchaToken: '',
            formSubmissionInProgress: false,
        };

        this.loadData = this.loadData.bind(this);
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
                <DataLoader
                    url='/api/verified/agency'
                    loadComponentData={this.loadData}
                />
                {this.state.loaded &&
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
                    <h1>Update {this.state.name}</h1>
                    <AvForm onValidSubmit={this.handleValidSubmit}>
                        <AvGroup>
                            <Label for="agencyName">Agency name</Label>
                            <AvInput name="agencyName" value={this.state.name} disabled />
                            <FormText>
                                If you would like to change your agency name, please contact us.
                            </FormText>
                        </AvGroup>
                        <AvGroup>
                            <Label for="externalUrl">Website URL</Label>
                            <AvInput name="externalUrl" type="url" value={this.state.externalUrl} placeholder="http://yoursite.com" onChange={this.handleChange} />
                            <FormText>
                                Optional. If your agency has a website, enter its URL here. Example: http://www.cambridgelettings.com/
                            </FormText>
                        </AvGroup>
                        <AvGroup>
                            <Label for="postcode">Postcode</Label>
                            <AvInput name="postcode" value={this.state.postcode} onChange={this.handleChange} />
                            <FormText>
                                Optional. Please enter the postcode of your agency's primary office.
                            </FormText>
                        </AvGroup>
                        <Button color="primary">
                            Update your agency details
                        </Button>
                    </AvForm>
                </LoadingOverlay>
                }
            </Container>
        );
    }

    loadData(data) {
        this.setState({
            slug: data.slug,
            name: data.name,
            externalUrl: data.externalUrl,
            postcode: data.postcode,
            loaded: true,
        });
    }

    handleValidSubmit() {
        this.setState({formSubmissionInProgress: true});
        let payload = {
            externalUrl: this.state.externalUrl,
            postcode: this.state.postcode,
            captchaToken: '',
        };
        let component = this;
        grecaptcha.ready(function() {
            grecaptcha.execute(Constants.GOOGLE_RECAPTCHA_SITE_KEY, {action: 'submit'}).then(function(captchaToken) {
                payload.captchaToken = captchaToken;
                fetch('/api/verified/agency/' + component.state.slug, {
                    method: 'PUT',
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

export default UpdateAgency;