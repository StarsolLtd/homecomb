import React, { useRef, useState } from 'react'
import PropTypes from 'prop-types'

import { Label, Button, FormText, Container } from 'reactstrap'
import { AvForm, AvGroup, AvInput, AvFeedback } from 'availity-reactstrap-validation'

const CreateBranch = (props) => {
  const [branchName, setBranchName] = useState('')
  const [telephone, setTelephone] = useState('')
  const [email, setEmail] = useState('')

  const createBranchForm = useRef(null)

  const handleChange = (event) => {
    const target = event.target
    const value = target.type === 'checkbox' ? target.checked : target.value

    switch (target.name) {
      case 'branchName':
        setBranchName(value)
        break
      case 'telephone':
        setTelephone(value)
        break
      case 'email':
        setEmail(value)
        break
    }
  }

  const handleValidSubmit = () => {
    const payload = {
      branchName,
      telephone,
      email
    }
    props.submit(
      payload,
      '/api/verified/branch',
      'POST'
    )
    createBranchForm.current.reset()
  }

  return (
    <Container>
      <h1>Add a branch</h1>
      <p>
        Please complete the form below to add a new branch to your agency.
      </p>
      <AvForm onValidSubmit={handleValidSubmit} ref={createBranchForm}>
        <AvGroup>
          <Label for="branchName">Branch name</Label>
          <AvInput name="branchName" placeholder="Branch location. Example: Cambridge" required onChange={handleChange} />
          <AvFeedback>Please enter your name.</AvFeedback>
          <FormText>
            Please enter the location of your branch. Please use the city/town/locality name. Examples: <i>Cambridge</i> or <i>Shoreditch</i>.
          </FormText>
        </AvGroup>
        <AvGroup>
          <Label for="telephone">Telephone</Label>
          <AvInput name="telephone" placeholder="Branch telephone number" onChange={handleChange} />
          <FormText>
            Optional. The telephone number of this branch. We will publish this.
          </FormText>
        </AvGroup>
        <AvGroup>
          <Label for="email">Email Address</Label>
          <AvInput name="email" placeholder="Example: branch@youragency.com" onChange={handleChange} />
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

CreateBranch.propTypes = {
  submit: PropTypes.func
}

export default CreateBranch
