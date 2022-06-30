import React, { useState } from 'react'
import AgencyBranch from '../components/AgencyBranch'
import { Breadcrumb, BreadcrumbItem, Col, Container, Row } from 'reactstrap'
import DataLoader from '../components/DataLoader'
import Constants from '../Constants'
import { HashLink as Link } from 'react-router-hash-link'
import PropTypes from 'prop-types'

const AgencyView = (props) => {
  const [agency, setAgency] = useState({})
  const [loaded, setLoaded] = useState(false)

  const loadData = (data) => {
    setAgency(data)
    setLoaded(true)
    document.title = data.name + ' Reviews | ' + Constants.SITE_NAME
  }

  return (
    <Container>
      <DataLoader
        url={'/api/agency/' + props.match.params.slug}
        loadComponentData={loadData}
      />
      {loaded &&
        <>
          <Row>
            <Breadcrumb className="w-100">
              <BreadcrumbItem><Link to="/#">{Constants.SITE_NAME}</Link></BreadcrumbItem>
              <BreadcrumbItem className="active agency-name">{agency.name}</BreadcrumbItem>
            </Breadcrumb>
          </Row>
          <Row className="bg-white rounded shadow-sm p-4 mb-4">
            <Col md={12}>
              {agency.branches.map(
                ({ slug, name, telephone, email }) => (
                  <AgencyBranch
                    key={slug}
                    slug={slug}
                    name={name}
                    telephone={telephone}
                    email={email}
                  />
                )
              )}
            </Col>
          </Row>
        </>
      }
    </Container>
  )
}

AgencyView.propTypes = {
  match: PropTypes.shape({
    params: PropTypes.shape({
      slug: PropTypes.string
    })
  })
}

export default AgencyView
