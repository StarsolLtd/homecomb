import React from 'react';
import ReactDOM from 'react-dom';
import {Input, Label, FormGroup, Form, Button} from 'reactstrap';
import Constants from "../Constants";

class CreateAgency extends React.Component {
    constructor() {
        super();
        this.state = {
            agencyName: '',
            externalUrl: '',
            postcode: '',
            captchaToken: ''
        };
        this.handleChange = this.handleChange.bind(this);
        this.handleSubmit = this.handleSubmit.bind(this);
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
            <Form onSubmit={this.handleSubmit}>
                <FormGroup>
                    <Label for="agencyName">Agency name</Label>
                    <Input name="agencyName" required onChange={this.handleChange} />
                </FormGroup>
                <FormGroup>
                    <Label for="externalUrl">Website URL</Label>
                    <Input name="externalUrl" placeholder="http://yoursite.com" onChange={this.handleChange} />
                </FormGroup>
                <FormGroup>
                    <Label for="postcode">Postcode</Label>
                    <Input name="postcode" onChange={this.handleChange} />
                </FormGroup>
                <Button type="submit"
                        className="btn btn-primary g-recaptcha"
                        data-sitekey="reCAPTCHA_site_key"
                        data-callback="onSubmit"
                        data-action="submit">Create an agency
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
                fetch(`/api/verified/agency`, {
                    method: 'POST',
                    body: JSON.stringify(payload),
                    headers: {
                        'Content-Type': 'application/json'
                    }
                }).then(res => res.json())
                    .then(data => console.log(data))
                    .catch(err => console.error("Error:", err));
            });
        });
        event.preventDefault();
    }
}

ReactDOM.render(<CreateAgency />, document.getElementById('create-agency-root'));