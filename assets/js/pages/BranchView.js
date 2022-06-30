import React, { useState } from 'react'
import { Breadcrumb, BreadcrumbItem, Col, Container, Row } from 'reactstrap'
import Review from '../components/Review'
import DataLoader from '../components/DataLoader'
import Constants from '../Constants'
import { HashLink as Link } from 'react-router-hash-link'
import PropTypes from 'prop-types'

const BranchView = (props) => {
  const [agency, setAgency] = useState({})
  const [branch, setBranch] = useState({})
  const [tenancyReviews, setTenancyReviews] = useState([])
  const [loaded, setLoaded] = useState(false)

  const loadData = (data) => {
    setAgency(data.agency)
    setBranch(data.branch)
    setTenancyReviews(data.tenancyReviews)
    setLoaded(true)
    document.title = data.branch.name + ' Branch Reviews | ' + Constants.SITE_NAME
  }

  return (
    <Container>
      <DataLoader
        url={'/api/branch/' + props.match.params.slug}
        loadComponentData={loadData}
      />
      {loaded &&
        <>
          <Row>
            <Breadcrumb className="w-100">
              <BreadcrumbItem><Link to="/#">{Constants.SITE_NAME}</Link></BreadcrumbItem>
              {agency &&
                <BreadcrumbItem className="agency-name"><Link to={'/agency/' + agency.slug}>{agency.name}</Link></BreadcrumbItem>
              }
              <BreadcrumbItem className="active branch-name">{branch.name}</BreadcrumbItem>
            </Breadcrumb>
          </Row>
          <Row className="bg-white rounded shadow-sm p-4 mb-4">
            <Col xs="12" md="8">
              <h5 className="mb-1">Reviews from tenants</h5>

              {tenancyReviews.length > 0 && tenancyReviews.map(
                ({ id, author, title, content, start, end, property, branch, agency, stars, createdAt, comments, positiveVotes }) => (
                  <Review
                    {...props}
                    key={id}
                    id={id}
                    author={author}
                    start={start}
                    end={end}
                    title={title}
                    content={content}
                    property={property}
                    branch={branch}
                    agency={agency}
                    stars={stars}
                    createdAt={createdAt}
                    comments={comments}
                    positiveVotes={positiveVotes}
                    showBranch={false}
                  />
                )
              ).reduce((prev, curr) => [prev, <hr key={'hr_' + prev.id} />, curr])}
            </Col>

            <Col md="4" className="d-sm-none d-md-block branch-agency">
              {agency.logoImageFilename &&
                <img src={'/images/images/' + agency.logoImageFilename} className="agency-logo" />
              }

              <h5 className="mb-1">{branch.name} contact details</h5>

              <p>
                {branch.telephone &&
                  <span>Telephone: {branch.telephone}<br /></span>
                }
                {branch.email &&
                  <span>Email: <a href={'mailto:' + branch.email}>{branch.email}</a><br /></span>
                }
              </p>
            </Col>
          </Row>
        </>
      }
    </Container>
  )
}

BranchView.propTypes = {
  match: PropTypes.shape({
    params: PropTypes.shape({
      slug: PropTypes.string
    })
  })
}

export default BranchView
