import React, { useRef, useState } from 'react'
import PropTypes from 'prop-types'
import { Container, Label, Button, FormText } from 'reactstrap'
import { AvForm, AvGroup, AvInput, AvFeedback } from 'availity-reactstrap-validation'
import InputProperty from '../../components/InputProperty'
import DataLoader from '../../components/DataLoader'

const CreateReviewSolicitation = (props) => {
  const [agency, setAgency] = useState(null)
  const [branches, setBranches] = useState([])
  const [branchSlug, setBranchSlug] = useState('')
  const [propertySlug, setPropertySlug] = useState('')
  const [recipientTitle] = useState('')
  const [recipientFirstName, setRecipientFirstName] = useState('')
  const [recipientLastName, setRecipientLastName] = useState('')
  const [recipientEmail, setRecipientEmail] = useState('')
  const [loaded, setLoaded] = useState(false)

  const form = useRef(null)

  const handleChange = (event) => {
    const target = event.target
    const value = target.type === 'checkbox' ? target.checked : target.value

    switch (target.name) {
      case 'branchSlug':
        setBranchSlug(value)
        break
      case 'recipientFirstName':
        setRecipientFirstName(value)
        break
      case 'recipientLastName':
        setRecipientLastName(value)
        break
      case 'recipientEmail':
        setRecipientEmail(value)
        break
    }
  }

  const handleValidSubmit = () => {
    const payload = {
      branchSlug,
      propertySlug,
      recipientTitle,
      recipientFirstName,
      recipientLastName,
      recipientEmail
    }

    props.submit(
      payload,
      '/api/verified/solicit-review',
      'POST'
    )

    form.current.reset()
  }

  const loadData = (data) => {
    setAgency(data.agency)
    setBranches(data.branches)
    setLoaded(true)
  }

  const setPropertySlugState = (value) => {
    if (typeof value !== 'undefined') {
      setPropertySlug(value)
    }
  }

  return (
    <Container>
      <DataLoader
        url={'/api/verified/solicit-review'}
        loadComponentData={loadData}
      />
      {loaded &&
        <>
          <h1>Request a review for {agency.name}</h1>
          <p>
            If you would like to request one of your tenant&apos;s review their tenancy with you, please complete
            the form below. We will send them an email with a unique link allowing them to review.
          </p>
          <AvForm id="solicit-review-form" onValidSubmit={handleValidSubmit} ref={form}>
            <AvGroup>
              <Label for="branchSlug">Branch</Label>
              <AvInput type="select" name="branchSlug" required onChange={handleChange}>
                <option value="" disabled>-Please select-</option>
                {branches.map(
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
              <AvInput type="hidden" name="propertySlug" required value={propertySlug} />
              <InputProperty
                inputId="input-property"
                source="/api/property/suggest-property"
                placeholder="Start typing a property address..."
                setPropertySlugState={setPropertySlugState}
              />
              <AvFeedback>Please enter a tenancy property address.</AvFeedback>
              <FormText>
                Please start typing the address of the tenancy, then select the correct address when it appears.
              </FormText>
            </AvGroup>
            <AvGroup>
              <Label for="recipientFirstName">Tenant first name</Label>
              <AvInput name="recipientFirstName" required value={recipientFirstName} onChange={handleChange} placeholder="Enter tenant first name" />
              <AvFeedback>Please enter the tenant&apos;s first name.</AvFeedback>
              <FormText>
                Please enter the first name of the tenant. Example: Jane.
              </FormText>
            </AvGroup>
            <AvGroup>
              <Label for="recipientLastName">Tenant surname</Label>
              <AvInput name="recipientLastName" required value={recipientLastName} onChange={handleChange} placeholder="Enter tenant surname" />
              <AvFeedback>Please enter the tenant&apos;s surname.</AvFeedback>
              <FormText>
                Please enter the surname of the tenant. Example: Smith.
              </FormText>
            </AvGroup>
            <AvGroup>
              <Label for="recipientEmail">Reviewer email</Label>
              <AvInput name="recipientEmail" type="email" required value={recipientEmail} onChange={handleChange} placeholder="Enter tenant email" />
              <AvFeedback>Please enter the tenant&apos;s email address.</AvFeedback>
              <FormText>
                Please enter the email address of the tenant. Example: jane.smith@domain.com
              </FormText>
            </AvGroup>
            <Button color="primary">
              Request review
            </Button>
          </AvForm>
        </>
      }
    </Container>
  )
}

CreateReviewSolicitation.propTypes = {
  submit: PropTypes.func
}

export default CreateReviewSolicitation
