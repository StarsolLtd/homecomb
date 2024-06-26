import React from 'react'
import { Row, Col } from 'reactstrap'
import DataLoader from '../../components/DataLoader'
import ReactTable from 'react-table-v6'
import 'react-table-v6/react-table.css'
import BranchCard from './BranchCard'
import AddBranchCard from './AddBranchCard'

export default class Dashboard extends React.Component {
  state = {
    agency: null,
    branches: [],
    reviews: [],
    loaded: false
  }

  render () {
    return (
      <>
        <DataLoader
          url='/api/verified/dashboard'
          loadComponentData={this.loadData}
        />
        {this.state.loaded &&
          <div id="dashboard">
            <h1>Agency Admin Area for {this.state.agency.name}</h1>

            <hr/>

            <h2>Your Branches</h2>

            <Row>
              {this.state.branches.map(
                ({ slug, name, telephone, email }) => (
                  <BranchCard key={slug} slug={slug} name={name}/>
                )
              )}
              <AddBranchCard/>
            </Row>

            <hr/>

            <Row className="bg-white rounded shadow-sm p-4 mb-4">
              <Col md={6} sm={12} className="mb-4">
                <h2 className="mb-4">Reviews of {this.state.agency.name}</h2>
                <ReactTable
                  data={this.state.reviews}
                  columns={[
                    { Header: 'Author', accessor: 'author' },
                    { Header: 'Property', accessor: 'property.addressLine1' }
                  ]}
                  defaultPageSize={10}
                  showPageSizeOptions={false}
                  className="-striped -highlight"
                />
              </Col>
            </Row>
          </div>
        }
      </>
    )
  }

  loadData = (data) => {
    this.setState({
      agency: data.agency,
      branches: data.branches,
      reviews: data.reviews,
      loaded: true
    })
  }
}
