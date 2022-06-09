import React from 'react'
import { shallow } from 'enzyme'
import Address from '../../assets/js/components/Address'

it('renders successfully and joins address parts as intended', () => {
  const wrapper = shallow(
        <Address
            addressLine1={'123 Test Lane'}
            addressLine2={'Testerton'}
            addressLine3={''}
            city={'Dereham'}
            postcode={'TE57 8DR'}
        />
  )
  expect(wrapper.find('a').text()).toContain('123 Test Lane, Testerton, Dereham, TE57 8DR')
})
