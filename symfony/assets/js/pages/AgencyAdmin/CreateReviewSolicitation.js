import React, {Fragment} from 'react';
import {Container, Label, Button, FormText} from 'reactstrap';
import { AvForm, AvGroup, AvInput, AvFeedback } from 'availity-reactstrap-validation';
import InputProperty from "../../components/InputProperty";
import DataLoader from "../../components/DataLoader";

class CreateReviewSolicitation extends React.Component {
    constructor(props) {
        super(props);
        this.state = {
            agency: null,
            branches: [],
            branchSlug: '',
            propertySlug: '',
            recipientTitle: '',
            recipientFirstName: '',
            recipientLastName: '',
            recipientEmail: '',
            loaded: false,
        };
        this.submit = this.props.submit;
        this.submit = this.submit.bind(this);

        this.handleChange = this.handleChange.bind(this);
        this.handleValidSubmit = this.handleValidSubmit.bind(this);
        this.setPropertySlugState = this.setPropertySlugState.bind(this);
        this.loadData = this.loadData.bind(this);
    }

    handleChange(event) {

        const target = event.target;
        const value = target.type === 'checkbox' ? target.checked : target.value;
        const name = target.name;

        this.setState({
            [name]: value
        });
    }

    loadData(data) {
        this.setState({
            agency: data.agency,
            branches: data.branches,
            loaded: true,
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
                <DataLoader
                    url={'/api/verified/solicit-review'}
                    loadComponentData={this.loadData}
                />
                {this.state.loaded &&
                <Fragment>
                    <h1>Request a review for {this.state.agency.name}</h1>
                    <p>
                        If you would like to request one of your tenant's review their tenancy with you, please complete
                        the form below. We will send them an email with a unique link allowing them to review.
                    </p>
                    <AvForm onValidSubmit={this.handleValidSubmit} ref={c => (this.form = c)}>
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
                            <AvInput name="recipientFirstName" required value={this.state.recipientFirstName} onChange={this.handleChange} placeholder="Enter tenant first name" />
                            <AvFeedback>Please enter the tenant's first name.</AvFeedback>
                            <FormText>
                                Please enter the first name of the tenant. Example: Jane.
                            </FormText>
                        </AvGroup>
                        <AvGroup>
                            <Label for="recipientLastName">Tenant surname</Label>
                            <AvInput name="recipientLastName" required value={this.state.recipientLastName} onChange={this.handleChange} placeholder="Enter tenant surname" />
                            <AvFeedback>Please enter the tenant's surname.</AvFeedback>
                            <FormText>
                                Please enter the surname of the tenant. Example: Smith.
                            </FormText>
                        </AvGroup>
                        <AvGroup>
                            <Label for="recipientEmail">Reviewer email</Label>
                            <AvInput name="recipientEmail" type="email" required value={this.state.recipientEmail} onChange={this.handleChange} placeholder="Enter tenant email" />
                            <AvFeedback>Please enter the tenant's email address.</AvFeedback>
                            <FormText>
                                Please enter the email address of the tenant. Example: jane.smith@domain.com
                            </FormText>
                        </AvGroup>
                        <Button color="primary">
                            Request review
                        </Button>
                    </AvForm>
                </Fragment>
                }
            </Container>
        );
    }

    handleValidSubmit() {
        let payload = {
            branchSlug: this.state.branchSlug,
            propertySlug: this.state.propertySlug,
            recipientTitle: this.state.recipientTitle,
            recipientFirstName: this.state.recipientFirstName,
            recipientLastName: this.state.recipientLastName,
            recipientEmail: this.state.recipientEmail,
        };

        this.submit(
            payload,
            '/api/verified/solicit-review',
            'POST',
        )

        this.clearForm();
    }

    clearForm() {
        this.form && this.form.reset();
    }
}

export default CreateReviewSolicitation;