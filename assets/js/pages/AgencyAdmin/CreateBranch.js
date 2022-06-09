import React from 'react'
import { Label, Button, FormText, Container } from 'reactstrap'
import { AvForm, AvGroup, AvInput, AvFeedback } from 'availity-reactstrap-validation'

export default class CreateBranch extends React.Component {
  state = {
    branchName: '',
    telephone: '',
    email: ''
  }

  constructor (props) {
    super(props)
    this.submit = this.props.submit
    this.submit = this.submit.bind(this)
  }

  handleChange = (event) => {
    const target = event.target
    const value = target.type === 'checkbox' ? target.checked : target.value
    const name = target.name

    this.setState({
      [name]: value
    })
  }

  render () {
    return (
      <Container>
        <h1>Add a branch</h1>
        <p>
          Please complete the form below to add a new branch to your agency.
        </p>
        <AvForm onValidSubmit={this.handleValidSubmit} ref={c => (this.form = c)}>
          <AvGroup>
            <Label for="branchName">Branch name</Label>
            <AvInput name="branchName" placeholder="Branch location. Example: Cambridge" required onChange={this.handleChange} />
            <AvFeedback>Please enter your name.</AvFeedback>
            <FormText>
              Please enter the location of your branch. Please use the city/town/locality name. Examples: <i>Cambridge</i> or <i>Shoreditch</i>.
            </FormText>
          </AvGroup>
          <AvGroup>
            <Label for="telephone">Telephone</Label>
            <AvInput name="telephone" placeholder="Branch telephone number" onChange={this.handleChange} />
            <FormText>
              Optional. The telephone number of this branch. We will publish this.
            </FormText>
          </AvGroup>
          <AvGroup>
            <Label for="email">Email Address</Label>
            <AvInput name="email" placeholder="Example: branch@youragency.com" onChange={this.handleChange} />
            <FormText>
              Optional. The email address of this branch. We will publish this.
            </FormText>
          </AvGroup>
          <Button color="primary">
            Add your branch
          </Button>
        </AvForm>
      </Container>
    )
  }

  handleValidSubmit = () => {
    const payload = {
      branchName: this.state.branchName,
      externalUrl: this.state.externalUrl,
      postcode: this.state.postcode
    }
    this.submit(
      payload,
      '/api/verified/branch',
      'POST'
    )
    this.clearForm()
  }

  clearForm = () => {
    this.form && this.form.reset()
  }
}
