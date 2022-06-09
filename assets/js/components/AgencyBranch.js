import React from 'react'
import PropTypes from 'prop-types'
import { HashLink as Link } from 'react-router-hash-link'

const AgencyBranch = (props) => {
  return (
    <div className="agency-branch">
      <div><Link to={'/branch/' + props.slug + '#'}>{props.name}</Link></div>
      {props.telephone &&
        <div className="telephone">Tel: {props.telephone}</div>
      }
      {props.email &&
        <div className="email">Email: <a href={'mailto:' + props.email}>{props.email}</a></div>
      }
    </div>
  )
}

AgencyBranch.propTypes = {
  slug: PropTypes.string,
  name: PropTypes.string,
  telephone: PropTypes.string,
  email: PropTypes.string
}

export default AgencyBranch
