import React, { useState } from 'react'
import PropTypes from 'prop-types'
import { Label, FormText, Button, Container } from 'reactstrap'
import DataLoader from '../../components/DataLoader'
import { AvForm, AvGroup, AvInput } from 'availity-reactstrap-validation'

const UpdateBranch = (props) => {
  const [name, setName] = useState('')
  const [telephone, setTelephone] = useState('')
  const [email, setEmail] = useState('')
  const [loaded, setLoaded] = useState(false)

  const loadData = (data) => {
    setName(data.name)
    setTelephone(data.telephone)
    setEmail(data.email)
    setLoaded(true)
  }

  const handleChange = (event) => {
    const target = event.target
    const value = target.type === 'checkbox' ? target.checked : target.value

    switch (target.name) {
      case 'name':
        setName(value)
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
      telephone,
      email
    }
    props.submit(
      payload,
      '/api/verified/branch/' + props.computedMatch.params.slug,
      'PUT'
    )
  }

  return (
    <Container>
      <DataLoader
        url={'/api/verified/branch/' + props.computedMatch.params.slug}
        loadComponentData={loadData}
      />
      {loaded &&
        <>
          <h1>Update {name}</h1>
          <AvForm onValidSubmit={handleValidSubmit}>
            <AvGroup>
              <Label for="name">Branch name</Label>
              <AvInput name="name" value={name} disabled />
              <FormText>
                If you would like to change your branch name, please contact us.
              </FormText>
            </AvGroup>
            <AvGroup>
              <Label for="telephone">Telephone</Label>
              <AvInput name="telephone" value={telephone} placeholder="Branch telephone number" onChange={handleChange} />
              <FormText>
                Optional. The telephone number of this branch. We will publish this.
              </FormText>
            </AvGroup>
            <AvGroup>
              <Label for="email">Email Address</Label>
              <AvInput name="email" value={email} placeholder="Example: branch@youragency.com" onChange={handleChange} />
              <FormText>
                Optional. The email address of this branch. We will publish this.
              </FormText>
            </AvGroup>
            <Button color="primary">
              Update your branch details
            </Button>
          </AvForm>
        </>
      }
    </Container>
  )
}

UpdateBranch.propTypes = {
  submit: PropTypes.func,
  computedMatch: PropTypes.shape({
    params: PropTypes.shape({
      slug: PropTypes.string
    })
  })
}

export default UpdateBranch
