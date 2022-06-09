import React from 'react'
import { Redirect } from 'react-router-dom'

export default class AgentRoute extends React.Component {
  render () {
    const Component = this.props.render
    return (
      <>
        {this.props.isAgencyAdmin &&
          <Component {...this.props} />
        }
        {!this.props.isAgencyAdmin &&
          <Redirect to={{ pathname: '/verified/agency/create', state: { from: this.props.location } }} />
        }
      </>
    )
  }
}
