import React, { useState } from 'react'
import PropTypes from 'prop-types'
import { Label, FormText, Button, Container } from 'reactstrap'
import DataLoader from '../../components/DataLoader'
import { AvForm, AvGroup, AvInput } from 'availity-reactstrap-validation'

const UpdateAgency = (props) => {
  const [slug, setSlug] = useState('')
  const [name, setName] = useState('')
  const [externalUrl, setExternalUrl] = useState('')
  const [postcode, setPostcode] = useState('')
  const [loaded, setLoaded] = useState(false)

  const loadData = (data) => {
    setSlug(data.slug)
    setName(data.name)
    setExternalUrl(data.externalUrl)
    setPostcode(data.postcode)
    setLoaded(true)
  }

  const handleChange = (event) => {
    const target = event.target
    const value = target.type === 'checkbox' ? target.checked : target.value

    switch (target.name) {
      case 'name':
        setName(value)
        break
      case 'externalUrl':
        setExternalUrl(value)
        break
      case 'postcode':
        setPostcode(value)
        break
    }
  }

  const handleValidSubmit = () => {
    const payload = {
      externalUrl,
      postcode
    }
    props.submit(
      payload,
      '/api/verified/agency/' + slug,
      'PUT'
    )
  }

  return (
    <Container>
      <DataLoader url='/api/verified/agency' loadComponentData={loadData}/>
      {loaded &&
        <>
          <h1>Update {name}</h1>
          <AvForm id="update-agency-form" onValidSubmit={handleValidSubmit}>
            <AvGroup>
              <Label for="agencyName">Agency name</Label>
              <AvInput name="agencyName" value={name} disabled />
              <FormText>
                If you would like to change your agency name, please contact us.
              </FormText>
            </AvGroup>
            <AvGroup>
              <Label for="externalUrl">Website URL</Label>
              <AvInput name="externalUrl" type="url" value={externalUrl} placeholder="http://yoursite.com" onChange={handleChange} />
              <FormText>
                Optional. If your agency has a website, enter its URL here. Example: http://www.cambridgelettings.com/
              </FormText>
            </AvGroup>
            <AvGroup>
              <Label for="postcode">Postcode</Label>
              <AvInput name="postcode" value={postcode} onChange={handleChange} />
              <FormText>
                Optional. Please enter the postcode of your agency&apos;s primary office.
              </FormText>
            </AvGroup>
            <Button color="primary">
              Update your agency details
            </Button>
          </AvForm>
        </>
      }
    </Container>
  )
}

UpdateAgency.propTypes = {
  submit: PropTypes.func
}

export default UpdateAgency
