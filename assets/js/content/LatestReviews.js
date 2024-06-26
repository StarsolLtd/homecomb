import React, { useState } from 'react'
import { Col, Container, Row } from 'reactstrap'
import DataLoader from '../components/DataLoader'
import Review from '../components/Review'

import '../../styles/latest-reviews.scss'

const LatestReviews = () => {
  const [tenancyReviews, setTenancyReviews] = useState([])
  const [loaded, setLoaded] = useState(false)

  const loadData = (data) => {
    setTenancyReviews(data.tenancyReviews)
    setLoaded(true)
  }

  return (
    <div className="latest-reviews">
      <Container className="p-4 pt-5 pb-5">
        <h2 className="text-center">Recent Reviews</h2>
        <p className="text-center">
          Here is what tenants have had to say about their renting experiences recently:
        </p>
        <Row>
          <DataLoader
            url={'/api/review/latest'}
            loadComponentData={loadData}
          />
          {loaded &&
            tenancyReviews.map(
              ({ id, author, start, end, title, content, property, branch, agency, stars, createdAt }) => (
                <Col key={id} lg={4} md={6} sm={12} className="d-flex">
                  <Col md={12} className="bg-white rounded shadow-sm pl-3 pr-4 m-1">
                    <Review
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
                      showBranch={false}
                      showOptions={false}
                      showVote={false}
                    />
                  </Col>
                </Col>
              )
            )
          }
        </Row>
      </Container>
    </div>
  )
}

export default LatestReviews
