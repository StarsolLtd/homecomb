import React, { Fragment } from 'react'
import AgencyBranch from '../components/AgencyBranch'
import { Breadcrumb, BreadcrumbItem, Col, Container, Row } from 'reactstrap'
import DataLoader from '../components/DataLoader'
import Constants from '../Constants'
import { HashLink as Link } from 'react-router-hash-link'

export default class AgencyView extends React.Component {
  state = {
    agency: {},
    loaded: false
  }

  render () {
    return (
      <Container>
        <DataLoader
          url={'/api/agency/' + this.props.match.params.slug}
          loadComponentData={this.loadData}
        />
        {this.state.loaded &&
          <Fragment>
            <Row>
              <Breadcrumb className="w-100">
                <BreadcrumbItem><Link to="/#">{Constants.SITE_NAME}</Link></BreadcrumbItem>
                <BreadcrumbItem className="active agency-name">{this.state.agency.name}</BreadcrumbItem>
              </Breadcrumb>
            </Row>
            <Row className="bg-white rounded shadow-sm p-4 mb-4">
              <Col md={12}>
                {this.state.agency.branches.map(
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
          </Fragment>
        }
      </Container>
    )
  }

  loadData = (data) => {
    this.setState({
      agency: data,
      loaded: true
    })
    document.title = this.state.agency.name + ' Reviews | ' + Constants.SITE_NAME
  }
}
