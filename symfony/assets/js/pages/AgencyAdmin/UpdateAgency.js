import React from 'react';
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
            captchaToken: ''
        };

        this.addFlashMessage = this.props.addFlashMessage;
        this.submit = this.props.submit;

        this.addFlashMessage = this.addFlashMessage.bind(this);
        this.submit = this.submit.bind(this);

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
                    active={this.props.formSubmissionInProgress}
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
        let payload = {
            externalUrl: this.state.externalUrl,
            postcode: this.state.postcode,
            captchaToken: '',
        };
        this.submit(
            payload,
            '/api/verified/agency/' + this.state.slug,
            'PUT',
            'Your agency was updated successfully.'
        )
    }
}

export default UpdateAgency;