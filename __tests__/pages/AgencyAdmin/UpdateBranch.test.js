import React from 'react'
import { shallow } from 'enzyme'
import UpdateBranch from '../../../assets/js/pages/AgencyAdmin/UpdateBranch'

it('renders without crashing', () => {
  const computedMatch = {
    params: {
      slug: 'test'
    }
  }
  shallow(<UpdateBranch computedMatch={computedMatch} />)
})
