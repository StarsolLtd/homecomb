import React, { Fragment } from 'react'
import { Label, FormText, Button, Container } from 'reactstrap'
import DataLoader from '../../components/DataLoader'
import { AvForm, AvGroup, AvInput } from 'availity-reactstrap-validation'

export default class UpdateBranch extends React.Component {
  state = {
    name: '',
    telephone: '',
    email: '',
    loaded: false
  }

  constructor (props) {
    super(props)
    this.submit = this.props.submit
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
        <DataLoader
          url={'/api/verified/branch/' + this.props.computedMatch.params.slug}
          loadComponentData={this.loadData}
        />
        {this.state.loaded &&
          <Fragment>
            <h1>Update {this.state.name}</h1>
            <AvForm onValidSubmit={this.handleValidSubmit}>
              <AvGroup>
                <Label for="name">Branch name</Label>
                <AvInput name="name" value={this.state.name} disabled />
                <FormText>
                  If you would like to change your branch name, please contact us.
                </FormText>
              </AvGroup>
              <AvGroup>
                <Label for="telephone">Telephone</Label>
                <AvInput name="telephone" value={this.state.telephone} placeholder="Branch telephone number" onChange={this.handleChange} />
                <FormText>
                  Optional. The telephone number of this branch. We will publish this.
                </FormText>
              </AvGroup>
              <AvGroup>
                <Label for="email">Email Address</Label>
                <AvInput name="email" value={this.state.email} placeholder="Example: branch@youragency.com" onChange={this.handleChange} />
                <FormText>
                  Optional. The email address of this branch. We will publish this.
                </FormText>
              </AvGroup>
              <Button color="primary">
                Update your branch details
              </Button>
            </AvForm>
          </Fragment>
        }
      </Container>
    )
  }

  loadData = (data) => {
    this.setState({
      name: data.name,
      telephone: data.telephone,
      email: data.email,
      loaded: true
    })
  }

  handleValidSubmit = () => {
    const payload = {
      telephone: this.state.telephone,
      email: this.state.email
    }
    this.submit(
      payload,
      '/api/verified/branch/' + this.props.computedMatch.params.slug,
      'PUT'
    )
  }
}
