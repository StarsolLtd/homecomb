import React from 'react';
import {Label, Button, FormText, Container} from 'reactstrap';
import { AvForm, AvGroup, AvInput, AvFeedback } from 'availity-reactstrap-validation';

class CreateAgency extends React.Component {
    constructor(props) {
        super(props);
        this.state = {
            agencyName: '',
            externalUrl: '',
            postcode: '',
            captchaToken: '',
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
                <h1>Create an agency</h1>
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
            </Container>
        );
    }

    handleValidSubmit() {
        let payload = {
            agencyName: this.state.agencyName,
            externalUrl: this.state.externalUrl,
            postcode: this.state.postcode,
            captchaToken: '',
        };
        this.submit(
            payload,
            '/api/verified/agency',
            'POST',
            'Your agency was created successfully.'
        )
    }
}

export default CreateAgency;
