import React from 'react';
import {Label, Button, FormText, Container} from 'reactstrap';
import { AvForm, AvGroup, AvInput, AvFeedback } from 'availity-reactstrap-validation';
import Constants from "../../Constants";

class CreateAgency extends React.Component {
    constructor(props) {
        super(props);
        this.state = {
            agencyName: '',
            externalUrl: '',
            postcode: '',
        };
        this.submit = this.props.submit;
        this.submit = this.submit.bind(this);

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
                <h1>Add your agency to {Constants.SITE_NAME}</h1>
                <p>
                    Are you a Lettings Agent? If so, we'd love it if you added your agency to {Constants.SITE_NAME}!
                </p>
                <p>
                    Please complete the form below with details about your company.
                </p>
                <p>
                    This first form is about your company as a whole, not an individual branch or office.
                    You will be able to add these next.
                </p>
                <hr />
                <AvForm onValidSubmit={this.handleValidSubmit} ref={c => (this.form = c)}>
                    <AvGroup>
                        <Label for="agencyName">Agency name</Label>
                        <AvInput name="agencyName" required placeholder="Example: Cambridge Lettings" onChange={this.handleChange} />
                        <AvFeedback>Please enter your agency name.</AvFeedback>
                        <FormText>
                            Please enter the trading name of your agency. Example: Cambridge Lettings.
                        </FormText>
                    </AvGroup>
                    <AvGroup>
                        <Label for="externalUrl">Website URL</Label>
                        <AvInput name="externalUrl" type="url" placeholder="Example: http://yoursite.com" onChange={this.handleChange} />
                        <FormText>
                            Optional. If your agency has a website, enter its URL here. Example: http://www.cambridgelettings.com/
                        </FormText>
                    </AvGroup>
                    <AvGroup>
                        <Label for="postcode">Postcode</Label>
                        <AvInput name="postcode" placeholder="Example: CB4 3LF" onChange={this.handleChange} />
                        <FormText>
                            Optional. Please enter the postcode of your agency's primary office. Example: CB4 3LF
                        </FormText>
                    </AvGroup>
                    <Button color="primary">
                        Add your agency
                    </Button>
                </AvForm>
                <hr />
            </Container>
        );
    }

    handleValidSubmit() {
        let payload = {
            agencyName: this.state.agencyName,
            externalUrl: this.state.externalUrl,
            postcode: this.state.postcode,
        };
        this.submit(
            payload,
            '/api/verified/agency',
            'POST',
            'Your agency was created successfully.',
            '/verified/agency-admin'
        )

        this.clearForm()
    }

    clearForm() {
        this.form && this.form.reset();
    }
}

export default CreateAgency;
