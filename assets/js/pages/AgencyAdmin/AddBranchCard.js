import React from 'react'
import { Link } from 'react-router-dom'

import { Col, Button, Card, Row } from 'reactstrap'

const AddBranchCard = (props) => {
  return (
    <Col sm="12" md="4">
      <Card className="mb-3 p-3">
        <Row className="no-gutters">
          <Link to={'/verified/branch'}>
            <Button color="primary">
              Add Branch
            </Button>
          </Link>
        </Row>
      </Card>
    </Col>
  )
}

export default AddBranchCard
